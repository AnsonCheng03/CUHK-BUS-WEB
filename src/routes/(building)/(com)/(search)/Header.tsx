import { component$, type Signal } from "@builder.io/qwik";
import styles from "./inputLocation.module.css";

export const Header = component$(
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
