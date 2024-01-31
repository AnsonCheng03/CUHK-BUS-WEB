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
      searchRoute: string[];
      requiredTime: (string | number)[];
    };
  }) => {
    const availableRoutes = useSignal<
      [string, "normal" | "delay" | "suspended" | "no"][]
    >([]);

    const fetchBusStatus = $(() => {
      return fetch("/Data/Status.json", { cache: "no-store" });
      // return fetch("https://cu-bus.online/Data/Status.json", {
      //   cache: "no-store",
      // });
    });

    const fetchBusStatusTask = $(async () => {
      const res = await fetchBusStatus();
      if (!res.ok)
        res.json = async () => {
          return {
            "1": {},
            "2": {
              網路錯誤: "no",
            },
            // "2022-09-06 00:28:22": {
            //   ERROR: "fetch",
            // },
          };
        };
      const data = await res.json();
      if (!data) return;
      const allRoutes = Object.entries(data).sort().pop();
      if (!allRoutes) return;
      const routes = allRoutes[1];
      if (!routes) return;
      availableRoutes.value = Object.entries(routes);
      // update searchSettings.searchRoute
      searchSettings.searchRoute = availableRoutes.value
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
  },
);
