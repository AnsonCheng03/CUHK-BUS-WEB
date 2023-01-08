import Navbar from './essentials/Navbar';
import Building from './features/Building';

function App() {

  return (
    <div className="App">
      <Navbar />
      <div className="content">
        <Building />
      </div>
    </div>
  );
}

export default App;
