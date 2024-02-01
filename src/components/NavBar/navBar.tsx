import { component$ } from "@builder.io/qwik";
import styles from "./navBar.module.css";
import { Link } from "@builder.io/qwik-city";

export default component$(() => {
  return (
    <nav>
      <input type="checkbox" />
      <div class={styles.hamburger}>
        <div class={styles.bar}></div>
      </div>
      <h1 class={styles.logo}>中大校巴資訊站</h1>
      <div class={styles.blur}></div>
      <ul>
        <li>
          <Link href="/" class={styles.active}>
            路線查詢
          </Link>
        </li>
        <li>
          <Link href="/">到站時間</Link>
        </li>
        <li>
          <Link href="/">有用Links</Link>
        </li>
      </ul>
    </nav>
  );
});
