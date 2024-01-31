import {
  component$,
  type Signal,
  type QwikIntrinsicElements,
} from "@builder.io/qwik";

import styles from "./modal.module.css";

export const GPSModal = component$(
  ({
    showSig,
  }: {
    showSig: Signal<[string, [string, number][]] | [[], []]>;
  }) => {
    return (
      showSig.value[1].length > 0 && (
        <div class={styles.modal}>
          <div class={styles.modalContent}>
            <div class={styles.modalHeader}>
              <h2 class={styles.modalTitle}>最近之車站</h2>
              <button
                class={styles.modalClose}
                onClick$={() => {
                  showSig.value = [[], []];
                }}
              >
                <CloseIcon class={styles.modalCloseIcon} />
              </button>
            </div>
            <div class={styles.modalBody}>
              {showSig.value[1].map((station) => {
                return (
                  <div class={styles.modalStation} key={station[0]}>
                    <p class={styles.modalStationName}>{station[0]}</p>
                    <p class={styles.modalStationDistance}>{station[1]}m</p>
                  </div>
                );
              })}
            </div>
          </div>
        </div>
      )
    );
  }
);

export function CloseIcon(props: QwikIntrinsicElements["svg"], key: string) {
  return (
    <svg
      xmlns="http://www.w3.org/2000/svg"
      viewBox="0 0 24 24"
      width={24}
      height={24}
      {...props}
      key={key}
    >
      <path
        fill="currentColor"
        d="m12 13.4l2.9 2.9q.275.275.7.275t.7-.275q.275-.275.275-.7t-.275-.7L13.4 12l2.9-2.9q.275-.275.275-.7t-.275-.7q-.275-.275-.7-.275t-.7.275L12 10.6L9.1 7.7q-.275-.275-.7-.275t-.7.275q-.275.275-.275.7t.275.7l2.9 2.9l-2.9 2.9q-.275.275-.275.7t.275.7q.275.275.7.275t.7-.275l2.9-2.9Zm0 8.6q-2.075 0-3.9-.788t-3.175-2.137q-1.35-1.35-2.137-3.175T2 12q0-2.075.788-3.9t2.137-3.175q1.35-1.35 3.175-2.137T12 2q2.075 0 3.9.788t3.175 2.137q1.35 1.35 2.138 3.175T22 12q0 2.075-.788 3.9t-2.137 3.175q-1.35 1.35-3.175 2.138T12 22Zm0-2q3.35 0 5.675-2.325T20 12q0-3.35-2.325-5.675T12 4Q8.65 4 6.325 6.325T4 12q0 3.35 2.325 5.675T12 20Zm0-8Z"
      ></path>
    </svg>
  );
}
