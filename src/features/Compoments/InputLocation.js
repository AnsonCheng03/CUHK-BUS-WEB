import "./InputLocation.css";
import GPSIcon from "../Assets/GPS.jpg";

const InputLocation = () => {
  return (
    <div className="input-location">
      <div className="input-location-header-container">
        <h2 className="input-location-header-title">路線查詢</h2>
        <div className="input-location-header-radio">
          <div className="input-location-mode-radio-container">
            <input
              className="input-location-mode-radio"
              type="radio"
              name="mode"
              id="mode"
              value="building"
            />
            <label className="input-location-mode-label" htmlFor="mode">
              <span className="input-location-mode-text">輸入建築</span>
            </label>
          </div>
          <div className="input-location-mode-radio-container">
            <input
              className="input-location-mode-radio"
              type="radio"
              name="mode"
              id="mode"
              value="station"
            />
            <label className="input-location-mode-label" htmlFor="mode">
              <span className="input-location-mode-text">選取站名</span>
            </label>
          </div>
        </div>
      </div>

      <div className="input-location-container">
        <div className="input-location-start">
          <div className="input-location-title-container">
            <span className="input-location-title">起點</span>
            <button className="input-location-button">
              <img src={GPSIcon} alt="GPSIcon" />
            </button>
          </div>
          <input
            className="input-location-input"
            type="text"
            name="start-location"
            id="start-location"
          />
        </div>

        <div className="input-location-end">
          <div className="input-location-title-container">
            <span className="input-location-title">終點</span>
            <button className="input-location-button">
              <img src={GPSIcon} alt="GPSIcon" />
            </button>
          </div>
          <input
            className="input-location-input"
            type="text"
            name="end-location"
            id="end-location"
          />
        </div>
      </div>
      <button className="input-submit">提交</button>
    </div>
  );
};

export default InputLocation;
