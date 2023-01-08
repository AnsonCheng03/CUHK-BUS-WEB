import "./Navbar.css";

const Navbar = () => {
  return (
    <nav>
      <input type="checkbox" />
      <div className="hamburger">
        <div className="bar"></div>
      </div>
      <p className="logo">中大校巴資訊站</p>
      <div className="blur"></div>
      <ul>
        <li>
          <a href="index.html" active="true">路線查詢</a>
        </li>
        <li>
          <a href="contact.html">到站時間</a>
        </li>
      </ul>
    </nav>
  );
};

export default Navbar;