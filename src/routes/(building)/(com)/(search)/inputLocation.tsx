import { $, component$, useSignal, useVisibleTask$ } from "@builder.io/qwik";
import styles from "./inputLocation.module.css";
import { autoComplete } from "./(component)/autoCompleteFunction";
import { GPSModal } from "./GPSModal";
import { Header } from "./Header";
import { InputLocation } from "./InputLocation.1";

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
