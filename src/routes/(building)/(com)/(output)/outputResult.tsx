import { component$, type Signal } from "@builder.io/qwik";

export default component$(
  ({
    result,
  }: {
    result: Signal<
      {
        id: number;
        BusNo: string;
        ArrivalTime: string[];
        Route: string[];
        Duration: number;
      }[]
    >;
  }) => {
    console.log(result.value);
    return <></>;
  }
);
