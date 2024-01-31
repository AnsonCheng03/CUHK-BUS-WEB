import styles from "./selectionSettings.module.css";

export const ShowAvailableRoutes = ({
  BusStatus,
}: {
  BusStatus: [string, "normal" | "delay" | "suspended" | "no"][];
}) => {
  return (
    <div class={styles.availableRoutes}>
      <div class={styles.availableRoutesTitleContainer}>
        <p class={styles.availableRoutesTitle}>校巴路線</p>
        <p class={[styles.availableRoutesTagExample, styles.normal]}>正常</p>
        <p class={[styles.availableRoutesTagExample, styles.delay]}>受阻</p>
        <p class={[styles.availableRoutesTagExample, styles.suspended]}>暫停</p>
        <p class={[styles.availableRoutesTagExample, styles.no]}>沒有服務</p>
      </div>
      <div class={styles.availableRoutesTagContainer}>
        {BusStatus.sort(
          (
            a: [string, "normal" | "delay" | "suspended" | "no"],
            b: [string, "normal" | "delay" | "suspended" | "no"],
          ) => {
            if (a[1] === b[1]) return a[0] < b[0] ? -1 : 1;
            if (a[1] === "delay") return -1;
            if (b[1] === "no") return -1;
            if (a[1] === "suspended" && b[1] !== "delay") return -1;
            return 1;
          },
        ).map((route: [string, "normal" | "delay" | "suspended" | "no"]) => {
          return (
            <p
              class={[styles.availableRoutesTag, styles[route[1]]]}
              key={route[0] as any}
            >
              {route[0]}
            </p>
          );
        })}
      </div>
    </div>
  );
};
