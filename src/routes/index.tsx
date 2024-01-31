import { component$, useSignal, useStore } from "@builder.io/qwik";
import type { DocumentHead } from "@builder.io/qwik-city";
import styles from "./index.module.css";
import SelectionSettings from "./(building)/(com)/(settings)/selectionSettings";
import InputLocation from "./(building)/(com)/(search)/inputLocation";
import OutputResult from "./(building)/(com)/(output)/outputResult";

export default component$(() => {
  const searchSettings = useStore({
    showAllRoutes: false,
    departNow: true,
    searchRoute: [""],
    requiredTime: [
      ["星期一", "星期二", "星期三", "星期四", "星期五", "星期六", "星期日"][
        new Date().getDay() - 1
      ],
      "教學日",
      new Date().getHours(),
      Math.ceil(new Date().getMinutes() / 5) * 5,
    ],
  });

  const busResult = useSignal([
    {
      id: 1,
      BusNo: "1A",
      ArrivalTime: ["23:00", "04:15", "03:30", "02:45"],
      Route: ["大學體育中心", "邵逸夫堂"],
      Duration: 30,
    },
  ]);

  return (
    <div class={styles.building}>
      <SelectionSettings searchSettings={searchSettings} />
      <InputLocation busResult={busResult} />
      <OutputResult result={busResult} />
    </div>
  );
});

export const head: DocumentHead = {
  title: "\u4e2d\u5927\u5df4\u58eb\u8cc7\u8a0a\u7ad9 CUHK BUS INFOPAGE",
  meta: [
    {
      name: "description",
      content:
        "\u4e2d\u5927\u5df4\u58eb\u8cc7\u8a0a\u7ad9\u63d0\u4f9b\u9ede\u5c0d\u9ede\u8def\u7dda\u641c\u5c0b\u3001\u5be6\u6642\u6821\u5df4\u67e5\u8a62\u670d\u52d9\uff0c\u8b93\u4f60\u8f15\u9b06\u5728\u4e2d\u5927\u6821\u5712\u7a7f\u68ad\u3002 CUHK Bus Infopage provides point-to-point route search and real-time school bus query services, allowing you to travel around the CUHK campus easily.",
    },
  ],
};
