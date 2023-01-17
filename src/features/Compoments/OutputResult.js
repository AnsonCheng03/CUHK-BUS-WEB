import { useState } from "react";
import "./OutputResult.css";

const OutputHeader = (prams) => {
  return (
    <div className="output-result-header">
      <p className="output-result-bus-no">{prams.BusNo}</p>
      <p className="output-result-duration">車程 {prams.Duration}分鐘</p>
    </div>
  );
};

const outputTimeHeaderOnCLick = (e) => {
  //get arrow attribute active
  const outputResultArrow = e.target.parentNode.querySelector(
    ".output-result-arrow"
  );
  const outputResultContainer = e.target.parentNode.parentNode.querySelector(
    ".output-result-arrival-time"
  );
  const active = outputResultArrow.getAttribute("active") === "active";

  //set btn status
  outputResultArrow.setAttribute("active", active ? false : "active");
  outputResultContainer.setAttribute("active", active ? false : "active");
};

const outputRouteHeaderOnClick = (e) => {
  //get arrow attribute active
  const outputResultArrow = e.target.parentNode.querySelector(
    ".output-result-arrow"
  );
  const outputResultContainer = e.target.parentNode.parentNode.querySelector(
    ".output-result-route-expand"
  );
  const active = outputResultArrow.getAttribute("active") === "active";

  console.log(outputResultContainer);
  //set btn status
  outputResultArrow.setAttribute("active", active ? false : "active");
  outputResultContainer.setAttribute("active", active ? false : "active");
};

const OutputArrivalTime = (prams) => {
  return (
    <div className="output-result-arrival-time-container">
      <div
        className="output-result-departure-time-header"
        onClick={outputTimeHeaderOnCLick}
      >
        <h3>到站</h3>
        {
          <div className="output-result-departure-time-container">
            <p>下一班</p>
            <p>{prams.nextBusArrivalTime}</p>
          </div>
        }
        <div className="output-result-arrow"></div>
      </div>

      <div className="output-result-arrival-time">
        <p className="output-result-arrival-time-title">下一班車</p>
        <div className="output-result-time-container">
          {prams.ArrivalTime.map((time) => {
            return (
              <p className="output-result-time" key={time}>
                {time}
              </p>
            );
          })}
        </div>
      </div>
    </div>
  );
};

const OutputResult = () => {
  const currentTime = new Date();
  const formattedCurrentTime =
    ("0" + currentTime.getHours()).slice(-2) +
    ":" +
    ("0" + currentTime.getMinutes()).slice(-2);

  const [BusResult, setBusResult] = useState([
    {
      id: 1,
      BusNo: "1A",
      ArrivalTime: ["23:00", "04:15", "03:30", "02:45"],
      Route: [
        "大學體育中心",
        "邵逸夫堂",
        "大學行政樓",
        "善衡書院",
        "地鐵大學站",
      ],
      Duration: 30,
    },
    {
      id: 2,
      BusNo: "1B",
      ArrivalTime: ["23:10", "09:15", "09:30", "09:45"],
      Route: ["大學體育中心", "邵逸夫堂", "善衡書院", "地鐵大學站"],
      Duration: 30,
    },
  ]);

  const OutputBusRoute = (prams) => {
    return (
      <div className="output-bus-route">
        <div className="output-result-route-header">
          <h3>路線</h3>
        </div>
        <div className="output-result-route-container">
            <div className="output-result-route-sidebar"></div>
          <div className="output-result-route-start">
            <h2>{prams.Route[0]}</h2>
          </div>
          <div
            className="output-result-route-collapse"
            onClick={outputRouteHeaderOnClick}
          >
            <p>共{prams.Route.length}個站</p>
            <div className="output-result-arrow"></div>
          </div>
          <div className="output-result-route-expand">
            {prams.Route.map((route) => {
              return <p key={route}>{route}</p>;
            })}
          </div>
          <div className="output-result-route-end">
            <h2>{prams.Route[prams.Route.length - 1]}</h2>
          </div>
        </div>
      </div>
    );
  };

  return (
    <div className="output-result">
      {BusResult.map((route) => {
        const nextBusArrivalTime = route.ArrivalTime.find((time) => {
          return time > formattedCurrentTime;
        });

        return (
          <div className="output-result-container" key={route.id}>
            <OutputHeader BusNo={route.BusNo} Duration={route.Duration} />

            <OutputBusRoute Route={route.Route} />

            <OutputArrivalTime
              ArrivalTime={route.ArrivalTime}
              nextBusArrivalTime={nextBusArrivalTime}
            />
          </div>
        );
      })}
    </div>
  );
};

export default OutputResult;
