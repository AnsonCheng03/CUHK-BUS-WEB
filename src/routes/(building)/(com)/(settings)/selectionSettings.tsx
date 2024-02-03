import { $, component$, useSignal, useVisibleTask$ } from "@builder.io/qwik";
import styles from "./selectionSettings.module.css";
import { ShowAvailableRoutes } from "./ShowAvailableRoutes";
import { Toggles } from "./Toggles";
import { TimePicker } from "./TimePicker";

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
    const availableRoutes = useSignal<
      [string, "normal" | "delay" | "suspended" | "no"][]
    >([]);

    const fetchBusStatus = $(() => {
      // return fetch("/Data/Status.json", { cache: "no-store" });
      return fetch("https://cu-bus.online/Data/Status.json", {
        cache: "no-store",
      });
    });

    const fetchBusStatusTask = $(async () => {
      const res = await fetchBusStatus();
      if (!res.ok) {
        searchSettings.searchRoute[0] = searchSettings.searchRoute[1] = [
          "ERROR",
        ];
        availableRoutes.value = [["網絡錯誤", "no"]];
        searchSettings.departNow = false;
        return;
      }
      const data = await res.json();
      if (!data) return;
      const allRoutes = Object.entries(data).sort();
      const latest30Routes = allRoutes.slice(-30).map(([, routes]) => routes);
      const [prevRoutes, currentRoutes] = [
        latest30Routes[0],
        latest30Routes[29],
      ];

      if (!currentRoutes) return;
      availableRoutes.value = Object.entries(currentRoutes);
      // update searchSettings.searchRoute
      searchSettings.searchRoute[0] = availableRoutes.value
        .filter(([, status]) => status !== "suspended" && status !== "no")
        .map(([route]) => route);
      if (!prevRoutes) return;
      searchSettings.searchRoute[1] = Object.entries(prevRoutes)
        .filter(([, status]) => status !== "suspended" && status !== "no")
        .map(([route]) => route);
    });

    useVisibleTask$(async () => {
      await fetchBusStatusTask();
      setInterval(async () => {
        await fetchBusStatusTask();
      }, 60000);
    });

    return (
      <div class={styles.busStatus}>
        <Toggles searchSettings={searchSettings} />
        {searchSettings.departNow === false && (
          <TimePicker searchSettings={searchSettings} />
        )}
        <ShowAvailableRoutes BusStatus={availableRoutes.value} />
      </div>
    );
  }
);
