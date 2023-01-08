import SelectionSettings from './Compoments/SelectionSettings';
import InputLocation from './Compoments/InputLocation.js';
import './Building.css';

const Building = () => {
  return (
    <div className="building">
      <SelectionSettings />
      <InputLocation />
    </div>
  );
};

export default Building;
