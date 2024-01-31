import styles from "./selectionSettings.module.css";

export const Toggles = ({
  searchSettings,
}: {
  searchSettings: {
    showAllRoutes: boolean;
    departNow: boolean;
    searchRoute: string[];
  };
}) => {
  return (
    <div class={styles.toggles}>
      <div class={styles.toggleAllRoutes}>
        <input
          type="checkbox"
          name="toggle-all-routes"
          id="toggle-all-routes"
          onChange$={() => {
            searchSettings.showAllRoutes = !searchSettings.showAllRoutes;
          }}
          checked={searchSettings.showAllRoutes}
        />
        <label for="toggle-all-routes">顯示轉車路線</label>
      </div>
      <div class={styles.toggleDepartNow}>
        <input
          type="checkbox"
          name="toggle-depart-now"
          id="toggle-depart-now"
          onChange$={() => {
            searchSettings.departNow = !searchSettings.departNow;
          }}
          checked={searchSettings.departNow}
        />
        <label for="toggle-depart-now">出發</label>
      </div>
    </div>
  );
};
