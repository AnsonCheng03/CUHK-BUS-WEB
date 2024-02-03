import { $, component$, type Signal } from "@builder.io/qwik";
import styles from "./outputResult.module.css";

// const outputTimeHeaderOnCLick = (e: any) => {
//   //get arrow attribute active
//   const outputResultArrow = e.target.parentNode.querySelector(
//     ".outputResult-arrow"
//   );
//   const outputResultContainer = e.target.parentNode.parentNode.querySelector(
//     ".outputResult-arrival-time"
//   );
//   const active = outputResultArrow.getAttribute("active") === "active";

//   //set btn status
//   outputResultArrow.setAttribute("active", active ? false : "active");
//   outputResultContainer.setAttribute("active", active ? false : "active");
// };

const outputRouteHeaderOnClick = $((e: any) => {
  //get arrow attribute active
  const outputResultArrow = e.target.parentNode.querySelector(
    `.${styles.outputResultArrow}`
  );
  const outputResultContainer = e.target.parentNode.parentNode.querySelector(
    `.${styles.outputResultRouteExpand}`
  );
  const active = outputResultArrow.getAttribute("active") === "active";
  //set btn status
  outputResultArrow.setAttribute("active", active ? false : "active");
  outputResultContainer.setAttribute("active", active ? false : "active");
});

// const OutputArrivalTime = (prams) => {
//   return (
//     <div class={styles.outputResultArrivalTimeContainer}>
//       <div
//         class={styles.outputResultDepartureTimeHeader}
//         onClick={outputTimeHeaderOnCLick}
//       >
//         <h3>到站</h3>
//         {
//           <div class={styles.outputResultDepartureTimeContainer}>
//             <p>下一班</p>
//             <p>{prams.nextBusArrivalTime}</p>
//           </div>
//         }
//         <div class={styles.outputResultArrow}></div>
//       </div>

//       <div class={styles.outputResultArrivalTime}>
//         <p class={styles.outputResultArrivalTimeTitle}>下一班車</p>
//         <div class={styles.outputResultTimeContainer}>
//           {prams.ArrivalTime.map((time) => {
//             return (
//               <p class={styles.outputResultTime} key={time}>
//                 {time}
//               </p>
//             );
//           })}
//         </div>
//       </div>
//     </div>
//   );
// };

// const OutputResult = ({ result }) => {
//   const currentTime = new Date();
//   const formattedCurrentTime =
//     ("0" + currentTime.getHours()).slice(-2) +
//     ":" +
//     ("0" + currentTime.getMinutes()).slice(-2);
// };

const RouteHeader = ({ BusNo, Time }: { BusNo: string; Time: number }) => (
  <div class={styles.outputResultHeader}>
    <p class={styles.outputResultBusNo}>{BusNo}</p>
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
  Details: { BusNo: string; Time: number; ArrivalTime: number[] };
}) => {
  let Time = Details.ArrivalTime[0];
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
              getFormattedTime(Details.ArrivalTime[0])
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
            {Route.map((route: (string | number | null)[]) => {
              return (
                <div key={route[0]} class={styles.outputResultBusStopContainer}>
                  <p class={styles.outputResultBusStop}>{route[0]}</p>
                  <p class={styles.outputResultBusArrivalTime}>
                    {getFormattedTime((Time += route[2] as number))}
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
          Details: { BusNo: string; Time: number; ArrivalTime: number[] };
        }[]
      | null
    >;
  }) => {
    if (result.value === null) return <></>;
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
  }
);
