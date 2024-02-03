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
    searchRoute: [[""], [""]],
    requiredTime: [
      ["星期一", "星期二", "星期三", "星期四", "星期五", "星期六", "星期日"][
        new Date().getDay() - 1
      ],
      "教學日",
      new Date().getHours(),
      Math.ceil(new Date().getMinutes() / 5) * 5,
    ],
  });

  const busResult = useSignal<
    | {
        Route: (string | number | null)[][];
        Details: { BusNo: string; Time: number; ArrivalTime: number[] };
      }[]
    | null
    | "Loading"
  >(
    null
    // [
    // {
    //   Route: [
    //     ["YIAP", null, 79.54756274],
    //     ["SPORTC", null, 126.0868326],
    //     ["SCIC", null, 80.00911399],
    //     ["FKHB", null, 669.1925832],
    //     ["RESI34", "UPPERST", 102.8883132],
    //     ["SHAWC", "UPPERST", 145.8532075],
    //     ["CWCC", "DOWNST", 61.57763302],
    //     ["RESI15", null, 42.44737859],
    //     ["UCSR", null, 53.2862827],
    //     ["CCHH", null, 141.2395914],
    //     ["SHAWC", "DOWNST", 74.04654848],
    //     ["RESI34", "DOWNST", 102.4130648],
    //     ["UADM", null, 117.3661056],
    //   ],
    //   Details: {
    //     BusNo: "3",
    //     Time: 1716.4066550799996,
    //     ArrivalTime: [1706781600, 1706782800, 1706784000],
    //   },
    // },
    // ]
  );

  return (
    <div class={styles.building}>
      <SelectionSettings searchSettings={searchSettings} />
      <InputLocation searchSettings={searchSettings} result={busResult} />
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
