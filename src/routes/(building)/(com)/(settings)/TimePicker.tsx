import styles from "./selectionSettings.module.css";

export const TimePicker = ({
  searchSettings,
}: {
  searchSettings: {
    requiredTime: (string | number)[];
  };
}) => {
  return (
    <div class={styles.timePicker}>
      <select
        class={styles.selectTime}
        name="Travel-wkday"
        id="Travel-wkday"
        value={searchSettings.requiredTime[0]}
        onChange$={(e: any) => {
          searchSettings.requiredTime[0] = e.target.value;
        }}
      >
        {[
          ["星期一", "WK-Mon"],
          ["星期二", "WK-Tue"],
          ["星期三", "WK-Wed"],
          ["星期四", "WK-Thu"],
          ["星期五", "WK-Fri"],
          ["星期六", "WK-Sat"],
          ["星期日", "WK-Sun"],
        ].map((day: any) => {
          return (
            <option value={`${day[1]}`} key={`${day[1]}`}>
              {`${day[0]}`}
            </option>
          );
        })}
      </select>
      {searchSettings.requiredTime[0] !== "WK-Sun" && (
        <select
          class={styles.selectTime}
          name="Travel-dayType"
          id="Travel-dayType"
          value={searchSettings.requiredTime[1]}
          onChange$={(e: any) => {
            searchSettings.requiredTime[1] = e.target.value;
          }}
        >
          {[
            ["教學日", "TD"],
            ["非教學日", "NT"],
            ["假日", "HD"],
          ].map((day: any) => {
            return (
              <option value={`${day[1]}`} key={`${day[1]}`}>
                {`${day[0]}`}
              </option>
            );
          })}
        </select>
      )}
      <div class={styles.timeGroup}>
        <select
          class={styles.selectTime}
          name="Travel-hour"
          id="Travel-hour"
          value={searchSettings.requiredTime[2]}
          onChange$={(e: any) => {
            searchSettings.requiredTime[2] = parseInt(e.target.value);
          }}
        >
          {[...Array(24).keys()].map((hour: any) => {
            hour = ("0" + hour).slice(-2);
            return (
              <option value={`${parseInt(hour)}`} key={`${hour}`}>
                {`${hour}`}
              </option>
            );
          })}
        </select>
        <span class={styles.timePickerColon}>:</span>
        <select
          class={styles.selectTime}
          name="Travel-minute"
          id="Travel-minute"
          value={searchSettings.requiredTime[3]}
          onChange$={(e: any) => {
            searchSettings.requiredTime[3] = parseInt(e.target.value);
          }}
        >
          {[...Array(60 / 5).keys()].map((minute: any) => {
            minute = ("0" + minute * 5).slice(-2);
            return (
              <option value={`${parseInt(minute)}`} key={`${minute}`}>
                {`${minute}`}
              </option>
            );
          })}
        </select>
      </div>
    </div>
  );
};
