import SelectionSettings from './Compoments/SelectionSettings';
import InputLocation from './Compoments/InputLocation.js';
import OutputResult from './Compoments/OutputResult.js';
import './Building.css';

const Building = () => {
  return (
    <div className="building">
      <SelectionSettings />
      <InputLocation />
      <OutputResult />
    </div>
  );
};

export default Building;
