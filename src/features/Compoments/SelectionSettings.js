import { useState, useEffect } from "react";
import "./SelectionSettings.css";

const Toggles = () => {
  return (
    <div className="toggles">
      <div className="toggle-all-routes">
        <input
          type="checkbox"
          name="toggle-all-routes"
          id="toggle-all-routes"
        />
        <label htmlFor="toggle-all-routes">顯示轉車路線</label>
      </div>
      <div className="toggle-depart-now">
        <input
          type="checkbox"
          name="toggle-depart-now"
          id="toggle-depart-now"
        />
        <label htmlFor="toggle-depart-now">出發</label>
      </div>
    </div>
  );
};

const TimePicker = () => {
  return (
    <div className="time-picker-container">
      <div className="weekday-picker">
        <span className="Travel-weekday-header">星期</span>
        <div className="weekday-picker-container">
          <input
            type="radio"
            name="Travel-weekday"
            id="weekday"
            value="Week-Mon"
          />
          <label htmlFor="weekday">一</label>
          <input
            type="radio"
            name="Travel-weekday"
            id="weekday"
            value="Week-Tue"
          />
          <label htmlFor="weekday">二</label>
          <input
            type="radio"
            name="Travel-weekday"
            id="weekday"
            value="Week-Wed"
          />
          <label htmlFor="weekday">三</label>
          <input
            type="radio"
            name="Travel-weekday"
            id="weekday"
            value="Week-Thu"
          />
          <label htmlFor="weekday">四</label>
          <input
            type="radio"
            name="Travel-weekday"
            id="weekday"
            value="Week-Fri"
          />
          <label htmlFor="weekday">五</label>
          <input
            type="radio"
            name="Travel-weekday"
            id="weekday"
            value="Week-Sat"
          />
          <label htmlFor="weekday">六</label>
          <input
            type="radio"
            name="Travel-weekday"
            id="weekday"
            value="Week-Sun"
          />
          <label htmlFor="weekday">日</label>
        </div>
        <span className="Travel-weekday-footer"></span>
      </div>
      <div className="date-picker">
        <span className="Travel-date-header"></span>
        <input type="radio" name="Travel-date" id="weekday" />
        <label htmlFor="weekday">教學日</label>
        <input type="radio" name="Travel-date" id="weekday" />
        <label htmlFor="weekday">非教學日</label>
        <input type="radio" name="Travel-date" id="weekend" />
        <label htmlFor="weekend">假日</label>
        <span className="Travel-date-footer"></span>
      </div>
      <div className="time-picker">
        <select className="select-time" name="Travel-hour" id="Travel-hour">
          {[...Array(24 - 6).keys()].map((hour) => {
            hour = ("0" + (hour + 6)).slice(-2);
            return ["00", "15", "30", "45"].map((minute) => {
              return (
                <option value={`${hour}:${minute}`} key={`${hour}:${minute}`}>
                  {`${hour}:${minute}`}
                </option>
              );
            });
          })}
        </select>
      </div>
    </div>
  );
};

const ShowAvailableRoutes = ({ BusStatus }) => {
  return (
    <div className="available-routes">
      <div className="available-routes-title-container">
        <p className="available-routes-title">校巴路線</p>
        <p className="available-routes-tag-example" status="normal">
          正常
        </p>
        <p className="available-routes-tag-example" status="delay">
          受阻
        </p>
        <p className="available-routes-tag-example" status="suspended">
          暫停
        </p>
        <p className="available-routes-tag-example" status="no">
          沒有服務
        </p>
      </div>
      <div className="available-routes-tag-container">
        {BusStatus.sort((a, b) => {
          if (a[1] === b[1]) return a[0] < b[0] ? -1 : 1;
          if (a[1] === "delay") return -1;
          if (b[1] === "no") return -1;
          if (a[1] === "suspended" && b[1] !== "delay") return -1;
          return 1;
        }).map((route) => {
          return (
            <p
              className="available-routes-tag"
              status={route[1]}
              key={route[0]}
            >
              {route[0]}
            </p>
          );
        })}
      </div>
    </div>
  );
};

const BusStatus = () => {
  const [availableRoutes, setAvailableRoutes] = useState([]);

  useEffect(() => {
    fetch("/data/Status.json")
      .then((res) => {
        return res.json();
      })
      .then((data) => {
        const routes = Object.entries(data).sort().pop()[1];
        setAvailableRoutes(Object.entries(routes));
      });
  }, []);

  return (
    <div className="bus-status">
      <Toggles />
      <TimePicker />
      <ShowAvailableRoutes BusStatus={availableRoutes} />
    </div>
  );
};

export default BusStatus;
