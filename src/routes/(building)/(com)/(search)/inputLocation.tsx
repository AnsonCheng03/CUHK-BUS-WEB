import { $, component$, useSignal, type Signal } from "@builder.io/qwik";
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
  }: {
    mode: Signal<"building" | "station">;
    type: string;
    inputLocation: Signal<string>;
    options: Signal<[string]>;
  }) => {
    return (
      <div class={styles.inputLocationStart}>
        <div class={styles.inputLocationTitleContainer}>
          <span class={styles.inputLocationTitle}>起點</span>
          <button
            class={styles.inputLocationButton}
            preventdefault:click
            onClick$={() => {
              if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition((position) => {
                  const lat = position.coords.latitude;
                  const lng = position.coords.longitude;
                  showSig.value = [
                    type,
                    [
                      ["大學體育中心", 1],
                      ["邵逸夫堂", 2],
                      ["賽馬會教學樓", 3],
                      ["賽馬會綜藝館", 4],
                    ],
                  ];
                });
              }
            }}
          >
            <img src={GPSIcon} alt="GPSIcon" />
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
              placeholder="卡號/電話號碼/中文姓名"
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
                autoComplete(input, options.value);
              }}
            />
          </div>
        ) : (
          <div class={styles.selectStation}>
            <select
              class={styles.selectStationSelect}
              name="start-station"
              id="start-station"
            >
              <option value="1">大學體育中心</option>
              <option value="2">邵逸夫堂</option>
              <option value="3">賽馬會教學樓</option>
              <option value="4">賽馬會綜藝館</option>
            </select>
          </div>
        )}
      </div>
    );
  }
);

export default component$(() => {
  const mode = useSignal<"building" | "station">("station");
  const startLocation = useSignal("");
  const endLocation = useSignal("");
  const options = useSignal([]);

  const showSig = useSignal<[string, [string, number][]] | [[], []]>([[], []]);

  return (
    <>
      <GPSModal showSig={showSig} />
      <form class={styles.inputLocation}>
        <Header mode={mode} />

        <div class={styles.inputLocationContainer}>
          <InputLocation
            mode={mode}
            type={"start"}
            inputLocation={startLocation}
            options={options}
          />

          <InputLocation
            mode={mode}
            type={"end"}
            inputLocation={endLocation}
            options={options}
          />
        </div>
        <button class={styles.inputSubmit}>提交</button>
      </form>
    </>
  );
});
