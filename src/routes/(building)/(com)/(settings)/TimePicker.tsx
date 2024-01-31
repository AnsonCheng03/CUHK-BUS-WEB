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
          "星期一",
          "星期二",
          "星期三",
          "星期四",
          "星期五",
          "星期六",
          "星期日",
        ].map((day: any) => {
          return (
            <option value={`${day}`} key={`${day}`}>
              {`${day}`}
            </option>
          );
        })}
      </select>
      {searchSettings.requiredTime[0] !== "星期日" && (
        <select
          class={styles.selectTime}
          name="Travel-dayType"
          id="Travel-dayType"
          value={searchSettings.requiredTime[1]}
          onChange$={(e: any) => {
            searchSettings.requiredTime[1] = e.target.value;
          }}
        >
          {["教學日", "非教學日", "假日"].map((day: any) => {
            return (
              <option value={`${day}`} key={`${day}`}>
                {`${day}`}
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
