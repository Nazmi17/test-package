import "flatpickr/dist/flatpickr.min.css";

import $ from "jquery";
import mask from "@alpinejs/mask";
import intersect from "@alpinejs/intersect";
import resize from "@alpinejs/resize";
import focus from "@alpinejs/focus";
import collapse from "@alpinejs/collapse";
import anchor from "@alpinejs/anchor";
import morph from "@alpinejs/morph";
import sort from "@alpinejs/sort";
import flatpickr from "flatpickr";

Alpine.plugin(mask);
Alpine.plugin(intersect);
Alpine.plugin(resize);
Alpine.plugin(focus);
Alpine.plugin(collapse);
Alpine.plugin(morph);
Alpine.plugin(anchor);
Alpine.plugin(sort);

(function () {
    // helper to try init newly injected nodes
    function initAlpineOn(el) {
        try {
            // Alpine v3 exposes initTree; if unavailable, fallback to start()
            if (typeof Alpine === "undefined") return;
            if (typeof Alpine.initTree === "function") {
                Alpine.initTree(el);
            } else {
                // fallback: re-start Alpine (safe because Alpine.start() is idempotent)
                Alpine.start();
            }
        } catch (e) {
            console.warn("Alpine init error", e);
        }
    }

    // On first load, nothing special — Alpine runs normally.
    // When Livewire navigates or morphs DOM, initialize Alpine on new root nodes.
    document.addEventListener("livewire:navigated", function (e) {
        // Livewire swaps a page root; try to find the root container and init
        // e.detail ? may not exist on all versions; fallback to document.body
        var root =
            e && e.detail && e.detail.root ? e.detail.root : document.body;
        initAlpineOn(root);
    });

    // Also handle Livewire partial updates (morphs)
    Livewire?.hook?.("message.processed", (message, component) => {
        // find any elements that look like our component and init them
        // we initialize the entire component node (component.el) if available
        try {
            if (component && component.el) {
                initAlpineOn(component.el);
            }
        } catch (err) {
            // ignore
        }
    });
})();
