import { $, component$, type Signal } from "@builder.io/qwik";
import styles from "./outputResult.module.css";

const outputRouteHeaderOnClick = $((e: any) => {
  //get arrow attribute active
  const outputResultArrow = e.target.parentNode.querySelector(
    `.${styles.outputResultArrow}`,
  );
  const outputResultContainer = e.target.parentNode.parentNode.querySelector(
    `.${styles.outputResultRouteExpand}`,
  );
  const active = outputResultArrow.getAttribute("active") === "active";
  //set btn status
  outputResultArrow.setAttribute("active", active ? false : "active");
  outputResultContainer.setAttribute("active", active ? false : "active");
});

const RouteHeader = ({
  BusNo,
  Time,
  Warning,
}: {
  BusNo: string;
  Time: number;
  Warning: string[] | null;
}) => (
  <div class={styles.outputResultHeader}>
    <p class={styles.outputResultBusNo}>{BusNo}</p>
    {Warning &&
      Warning.map((warning, index) => (
        <p key={index} class={styles.outputResultWarning}>
          {warning}
        </p>
      ))}
    <p class={styles.outputResultDuration}>
      車程 {(Time / 60).toFixed(0)} 分鐘
    </p>
  </div>
);

const getFormattedTime = (time: number) => {
  if (time === 0) return "暫無時間表";
  return new Date(time * 1000).toLocaleTimeString("en-US", {
    hour12: false,
    hour: "2-digit",
    minute: "2-digit",
    second: "2-digit",
  });
};

const OutputBusRoute = ({
  Route,
  Details,
}: {
  Route: (string | number | null)[][];
  Details: {
    BusNo: string;
    Time: number;
    ArrivalTime: number[] | null;
    Warning: string[] | null;
  };
}) => {
  let Time =
    Details.ArrivalTime && Details.ArrivalTime.length > 0
      ? Details.ArrivalTime[0]
      : 0;
  return (
    <div class={styles.outputBusRoute}>
      <div class={styles.outputResultRouteHeader}>
        <h3>路線</h3>
      </div>
      <div class={styles.outputResultRouteContainer}>
        <div class={styles.outputResultRouteSidebar}></div>
        <div class={styles.outputResultRouteStart}>
          <h2>{Route[0][0]}</h2>
          <span class={styles.outputResultRouteNextBus}>
            下一班車：
            {
              // Details.ArrivalTime[0] in format (xx:xx:xx, HKT Time) of unix timestamp
              getFormattedTime(Time)
            }
          </span>
        </div>
        <div
          class={styles.outputResultRouteCollapse}
          onClick$={outputRouteHeaderOnClick}
        >
          <p>共{Route.length}個站</p>
          <div class={styles.outputResultArrow}></div>
        </div>
        <div class={styles.outputResultRouteExpand}>
          <div class={styles.outputResultRouteExpandContainer}>
            {Route.map((route: (string | number | null)[], index: number) => {
              return (
                <div key={route[0]} class={styles.outputResultBusStopContainer}>
                  <p class={styles.outputResultBusStop}>{route[0]}</p>
                  <p class={styles.outputResultBusArrivalTime}>
                    {getFormattedTime(
                      (Time += (
                        index === 0 ? 0 : Route[index - 1][2]
                      ) as number),
                    )}
                  </p>
                </div>
              );
            })}
          </div>
        </div>
        <div class={styles.outputResultRouteEnd}>
          <h2>{Route[Route.length - 1][0]}</h2>
        </div>
      </div>
    </div>
  );
};

export default component$(
  ({
    result,
  }: {
    result: Signal<
      | {
          Route: (string | number | null)[][];
          Details: {
            BusNo: string;
            Time: number;
            ArrivalTime: number[] | null;
            Warning: string[] | null;
          };
        }[]
      | null
      | string
    >;
  }) => {
    if (result.value === null) return <></>;
    // if (result.value === "Loading")
    if (typeof result.value === "string")
      if (result.value === "Loading")
        return (
          <div class={styles.outputResult}>
            <div class={styles.outputResultContainer}>
              <p class={styles.outputResultNoResult}>Loading</p>
            </div>
          </div>
        );
      else
        return (
          <div class={styles.outputResult}>
            <div class={styles.outputResultContainer}>
              <p class={styles.outputResultNoResult}>{result.value}</p>
            </div>
          </div>
        );
    return (
      <div class={styles.outputResult}>
        {result.value.length === 0 ? (
          <div class={styles.outputResultContainer}>
            <p class={styles.outputResultNoResult}>沒有結果</p>
          </div>
        ) : (
          <>
            {result.value.map((route, index) => {
              console.log(route, index);
              // const nextBusArrivalTime = route.ArrivalTime.find((time) => {
              //   return time > formattedCurrentTime;
              // });

              return (
                <div class={styles.outputResultContainer} key={index}>
                  <RouteHeader
                    BusNo={route.Details.BusNo}
                    Time={route.Details.Time}
                    Warning={route.Details.Warning}
                  />
                  <OutputBusRoute Route={route.Route} Details={route.Details} />

                  {/* <OutputArrivalTime
                  ArrivalTime={route.ArrivalTime}
                  nextBusArrivalTime={nextBusArrivalTime}
                /> */}
                </div>
              );
            })}
          </>
        )}
      </div>
    );
  },
);
