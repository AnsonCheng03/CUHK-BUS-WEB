import { $, component$, useSignal, useVisibleTask$ } from "@builder.io/qwik";
import styles from "./inputLocation.module.css";
import { autoComplete } from "./(component)/autoCompleteFunction";
import { GPSModal } from "./GPSModal";
import { Header } from "./Header";
import { InputLocation } from "./InputLocationBox";

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

    useVisibleTask$(async ({ track }) => {
      track(() => mode.value);
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
          autoComplete(startInputField.value, autoCompleteField, startLocation);
        endInputField.value &&
          autoComplete(endInputField.value, autoCompleteField, endLocation);
      }, 150);
    });

    return (
      <>
        <GPSModal
          showSig={showSig}
          inputLocation={[startLocation, endLocation]}
        />
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
              const formData = new FormData();

              // Append search settings
              formData.append("action", "getRoute");
              formData.append(
                "showAllRoutes",
                searchSettings.showAllRoutes ? "1" : "0"
              );
              if (searchSettings.departNow) {
                formData.append(
                  "departNow",
                  JSON.stringify(searchSettings.searchRoute)
                );
              } else {
                formData.append(
                  "requiredTime",
                  JSON.stringify(searchSettings.requiredTime)
                );
              }

              // Append start and end location
              formData.append("mode", mode.value);

              if (mode.value === "building") {
                const startingLocation = startInputField.value
                  ? startInputField.value.value
                  : "";
                const endingLocation = endInputField.value
                  ? endInputField.value.value
                  : "";
                // Find the code of the building (get the content inside the last pair of bracket only)
                formData.append(
                  "startLocation",
                  startingLocation.slice(
                    startingLocation.lastIndexOf("(") + 1,
                    startingLocation.lastIndexOf(")")
                  )
                );
                formData.append(
                  "endLocation",
                  endingLocation.slice(
                    endingLocation.lastIndexOf("(") + 1,
                    endingLocation.lastIndexOf(")")
                  )
                );
              } else {
                formData.append("startLocation", startLocation.value);
                formData.append("endLocation", endLocation.value);
              }

              console.log("submit");
              for (const pair of formData.entries()) {
                console.log(pair[0] + ", " + pair[1]);
              }
            }}
          >
            提交
          </button>
        </form>
      </>
    );
  }
);
