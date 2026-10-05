const animations = new WeakMap();

// Native smooth scrolling has no duration control. Keep touch scrolling native;
// only animate arrow/dot navigation, and yield immediately to user input.
export function scrollCarousel(track, left, smooth = true) {
    animations.get(track)?.();
    const target = Math.max(0, Math.min(left, track.scrollWidth - track.clientWidth));
    const start = track.scrollLeft;
    const behavior = track.style.scrollBehavior;
    const snap = track.style.scrollSnapType;
    track.style.scrollBehavior = 'auto';
    track.style.scrollSnapType = 'none';
    let frame;
    const stop = () => {
        cancelAnimationFrame(frame);
        track.style.scrollBehavior = behavior;
        track.style.scrollSnapType = snap;
        ['pointerdown', 'wheel', 'keydown'].forEach(type => track.removeEventListener(type, stop));
        animations.delete(track);
    };
    if (!smooth || matchMedia('(prefers-reduced-motion: reduce)').matches) {
        track.scrollLeft = target;
        stop();
        return;
    }
    animations.set(track, stop);
    ['pointerdown', 'wheel', 'keydown'].forEach(type => track.addEventListener(type, stop, { passive: true }));
    const started = performance.now();
    const tick = now => {
        const progress = Math.min(1, (now - started) / 750);
        const eased = 1 - (1 - progress) ** 3;
        track.scrollLeft = start + (target - start) * eased;
        if (progress < 1) frame = requestAnimationFrame(tick);
        else stop();
    };
    frame = requestAnimationFrame(tick);
}
