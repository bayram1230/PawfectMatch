import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap";

import "./styles/app.css";

document.addEventListener("DOMContentLoaded", () => {
    const hero = document.querySelector(".hero-section");
    const outro = document.querySelector(".hero-outro");
    const video = document.getElementById("heroVideo");

    if (!hero || !outro || !video) return;

    const SHOW_DELAY = 2500;
    const CUT_SECONDS = 2;
    const FADE_DURATION = 1.8;

    let outroScheduled = false;

    video.pause();
    video.currentTime = 0;

    setTimeout(() => {
        video.classList.add("video-visible");
        video.play().catch(() => {});
    }, SHOW_DELAY);

    video.addEventListener("playing", () => {
        if (outroScheduled) return;
        outroScheduled = true;

        const duration = video.duration;
        if (!duration || isNaN(duration)) return;

        const fadeStartMs = (duration - CUT_SECONDS - FADE_DURATION) * 1000;

        setTimeout(() => {
            hero.classList.add("outro-active");
            outro.classList.add("active");
        }, fadeStartMs);

        setTimeout(() => {
            video.pause();
        }, fadeStartMs + FADE_DURATION * 1000);
    });
});
