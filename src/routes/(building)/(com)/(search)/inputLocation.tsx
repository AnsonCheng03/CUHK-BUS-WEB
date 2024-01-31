import {
  $,
  component$,
  useSignal,
  type Signal,
  useVisibleTask$,
} from "@builder.io/qwik";
import styles from "./inputLocation.module.css";
import GPSIcon from "../../(assets)/GPS.jpg";
import { autoComplete } from "./(component)/autoCompleteFunction";
import { GPSModal } from "./GPSModal";

const Header = component$(
  ({ mode }: { mode: Signal<"building" | "station"> }) => (
    <div class={styles.inputLocationHeaderContainer}>
      <h2 class={styles.inputLocationHeaderTitle}>路線查詢</h2>
      <div class={styles.inputLocationHeaderRadio}>
        <div class={styles.inputLocationModeRadioContainer}>
          <input
            class={styles.inputLocationModeRadio}
            type="radio"
            name="mode"
            id="mode"
            value="building"
            checked={mode.value === "building"}
            onChange$={() => {
              mode.value = "building";
            }}
          />
          <label class={styles.inputLocationModeLabel} for="mode">
            <span class={styles.inputLocationModeText}>輸入建築</span>
          </label>
        </div>
        <div class={styles.inputLocationModeRadioContainer}>
          <input
            class={styles.inputLocationModeRadio}
            type="radio"
            name="mode"
            id="mode"
            value="station"
            checked={mode.value === "station"}
            onChange$={() => {
              mode.value = "station";
            }}
          />
          <label class={styles.inputLocationModeLabel} for="mode">
            <span class={styles.inputLocationModeText}>選取站名</span>
          </label>
        </div>
      </div>
    </div>
  )
);

const InputLocation = component$(
  ({
    mode,
    type,
    inputLocation,
    options,
    inputField,
    showSig,
  }: {
    mode: Signal<"building" | "station">;
    type: string;
    inputLocation: Signal<string>;
    options: Signal<[string, string, "building" | "station"][]>;
    inputField: Signal<HTMLInputElement | null>;
    showSig: Signal<
      | [
          HTMLInputElement | Element | "Loading" | null,
          [string, string, number][],
        ]
      | [[], []]
    >;
  }) => {
    return (
      <div class={styles.inputLocationStart}>
        <div class={styles.inputLocationTitleContainer}>
          <span class={styles.inputLocationTitle}>
            {type === "start" ? "起點" : "終點"}
          </span>
          <button
            class={styles.inputLocationButton}
            preventdefault:click
            onClick$={() => {
              const formData = new FormData();
              formData.append("action", "getGPSNearest");

              showSig.value = ["Loading", []];
              navigator.geolocation.getCurrentPosition((position) => {
                formData.append("lat", position.coords.latitude.toString());
                formData.append("lng", position.coords.longitude.toString());

                const data = fetch(
                  "https://cu-bus.online/Essential/functions/api.php",
                  {
                    method: "POST",
                    body: formData,
                    cache: "no-store",
                    mode: "cors", // no-cors, *cors, same-origin
                    redirect: "follow", // manual, *follow, error
                    credentials: "same-origin", // include, *same-origin, omit
                    referrerPolicy: "no-referrer", // no-referrer, *no-referrer-when-downgrade,
                  }
                );

                data.then((res) => {
                  if (!res.ok) {
                    return;
                  }
                  res.json().then((data) => {
                    console.log(data);
                    showSig.value = [
                      mode.value === "building"
                        ? inputField.value
                        : document.querySelector(`#${type}-station`),
                      data.map(
                        ({
                          Location,
                          Name,
                          distance,
                        }: {
                          Location: string;
                          Name: string;
                          distance: number;
                        }) => [Location, Name, distance]
                      ),
                    ];
                  });
                });
              });
            }}
          >
            <img src={GPSIcon} alt="GPSIcon" width={24} height={24} />
          </button>
        </div>

        {mode.value === "building" ? (
          <div class={styles.autoComplete}>
            <input
              class={styles.inputLocationInput}
              type="text"
              autoComplete={"off"}
              name="start-location"
              id="start-location"
              bind:value={inputLocation}
            ></input>
            <img
              src="~/../LoadInputStart"
              width={0}
              height={0}
              onError$={(e: any) => {
                const img = e.target;
                const input = img.previousSibling;
                img && img.remove();
                input && input.focus();
                inputField.value = input;
              }}
            />
          </div>
        ) : (
          <div class={styles.selectStation}>
            <select
              class={styles.selectStationSelect}
              name={`${type}-station`}
              id={`${type}-station`}
            >
              {options.value
                .filter(([, , type]) => type === "station")
                .map(([code, name]) => (
                  <option value={code} key={code}>
                    {name}
                  </option>
                ))}
            </select>
          </div>
        )}
      </div>
    );
  }
);

export default component$(
  ({
    searchSettings,
  }: {
    searchSettings: {
      showAllRoutes: boolean;
      departNow: boolean;
      searchRoute: string[][];
      requiredTime: (string | number)[];
    };
  }) => {
    const mode = useSignal<"building" | "station">("building");
    const startLocation = useSignal("");
    const endLocation = useSignal("");
    const options = useSignal<[string, string, "building" | "station"][]>([]);
    const startInputField = useSignal<HTMLInputElement | null>(null);
    const endInputField = useSignal<HTMLInputElement | null>(null);

    const showSig = useSignal<
      | [
          HTMLInputElement | Element | "Loading" | null,
          [string, string, number][],
        ]
      | [[], []]
    >([[], []]);

    const fetchBusDetails = $(() => {
      const formData = new FormData();
      formData.append("action", "getData");

      return fetch("https://cu-bus.online/Essential/functions/api.php", {
        method: "POST",
        body: formData,
        cache: "no-store",
        mode: "cors", // no-cors, *cors, same-origin
        redirect: "follow", // manual, *follow, error
        credentials: "same-origin", // include, *same-origin, omit
        referrerPolicy: "no-referrer", // no-referrer, *no-referrer-when-downgrade,
      });
    });

    useVisibleTask$(async () => {
      const res = await fetchBusDetails();
      if (!res.ok) {
        return;
      }
      options.value = await res.json();

      const autoCompleteField: string[] = options.value.map(([code, name]) => {
        return `${name} (${code})`;
      });

      setTimeout(() => {
        startInputField.value &&
          autoComplete(startInputField.value, autoCompleteField);
        endInputField.value &&
          autoComplete(endInputField.value, autoCompleteField);
      }, 150);
    });

    return (
      <>
        <GPSModal showSig={showSig} mode={mode} />
        <form class={styles.inputLocation}>
          <Header mode={mode} />

          <div class={styles.inputLocationContainer}>
            <InputLocation
              mode={mode}
              type={"start"}
              inputLocation={startLocation}
              options={options}
              inputField={startInputField}
              showSig={showSig}
            />

            <InputLocation
              mode={mode}
              type={"end"}
              inputLocation={endLocation}
              options={options}
              inputField={endInputField}
              showSig={showSig}
            />
          </div>
          <button
            class={styles.inputSubmit}
            preventdefault:click
            onClick$={() => {
              console.log("submit", searchSettings);
            }}
          >
            提交
          </button>
        </form>
      </>
    );
  }
);
