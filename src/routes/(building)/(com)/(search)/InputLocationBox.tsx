import { component$, type Signal } from "@builder.io/qwik";
import styles from "./inputLocation.module.css";
import GPSIcon from "../../(assets)/GPS.jpg";

export const InputLocation = component$(
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
                  "http://localhost:8000/Essential/functions/api.php",
                  // "https://cu-bus.online/Essential/functions/api.php",
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
              autoComplete="off"
              autoCorrect="off"
              name={`${type}-location`}
              id={`${type}-location`}
              value={inputLocation.value}
              onChange$={(e: any) => {
                if (inputLocation.value !== e.target.value) e.target.value = "";
              }}
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
              bind:value={inputLocation}
            >
              {options.value
                .filter(([, , type]) => type === "station")
                .map(([code, name]) => (
                  <option value={`${name} (${code})`} key={code}>
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
