(function() {
    "use strict";

    const doc = document;
    const $ = (sel, root = doc) => root.querySelector(sel);
    const $$ = (sel, root = doc) => Array.from(root.querySelectorAll(sel));
    const Motion = {
        reduced() {
            return (
                window.matchMedia &&
                window.matchMedia("(prefers-reduced-motion: reduce)").matches
            );
        },
        hasGsap() {
            return !!window.gsap;
        },
        hasST() {
            return !!(window.gsap && window.ScrollTrigger);
        },
    };

    const REVEAL = {
        start: "top 95%",
        duration: 0.9,
        ease: "power2.out",
        y: 14,
    };

    function onReady(fn) {
        if (doc.readyState !== "loading") fn();
        else doc.addEventListener("DOMContentLoaded", fn);
    }

    function createOnceObserver({
        threshold = 0.2,
        rootMargin = "0px 0px -10% 0px"
    }, onEnter) {
        const obs = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                onEnter(entry.target, obs);
            });
        }, {
            threshold,
            rootMargin
        });
        return obs;
    }

    function revealOnScroll(targets, opts = {}) {
        if (!Motion.hasST() || Motion.reduced()) return;

        const o = {
            ...REVEAL,
            ...opts
        };
        const els = window.gsap.utils.toArray(targets);
        if (!els.length) return;

        els.forEach((el) => {
            const yFrom =
                o.yFrom != null ?
                o.yFrom :
                (el.classList.contains("scroll-fade-in-down") ? -o.y : o.y);

            window.gsap.fromTo(
                el, {
                    opacity: 0,
                    y: yFrom
                }, {
                    opacity: 1,
                    y: 0,
                    duration: 0.6,
                    ease: o.ease,
                    scrollTrigger: {
                        trigger: el,
                        start: "top 99%",
                        once: true,
                    },
                }
            );
        });
    }

    function initPriceReveal() {
        prepareChars(".anim-price-reveal");
        revealCharsOnScroll(".anim-price-reveal", {});

        if (window.ScrollTrigger) gsap.delayedCall(0.1, () => ScrollTrigger.refresh());
    }

    function prepareChars(selector) {
        const els = $$(selector);
        if (!els.length) return;

        els.forEach((el) => {
            if (el.classList.contains("is-ready")) return;

            el.style.visibility = "hidden";

            const text = (el.textContent || "");
            el.textContent = "";

            for (let i = 0; i < text.length; i++) {
                const char = text[i];
                const span = doc.createElement("span");
                span.className = "anim-char";
                span.style.setProperty("--char-index", i);
                span.textContent = char === " " ? "\u00A0" : char;
                el.appendChild(span);
            }

            el.classList.add("is-ready");
            el.style.visibility = "";
        });
    }

    function revealCharsOnScroll(selector, opts = {}) {
        if (!Motion.hasST() || Motion.reduced()) return;

        const o = {
            start: REVEAL.start,
            duration: 0.35,
            ease: REVEAL.ease,
            y: 22,
            stagger: 0.04,
            ...opts,
        };

        window.gsap.utils.toArray(selector).forEach((el) => {
            const chars = el.querySelectorAll(".anim-char");
            if (!chars.length) return;

            gsap.set(chars, {
                opacity: 0,
                y: o.y
            });

            gsap.to(chars, {
                opacity: 1,
                y: 0,
                duration: o.duration,
                ease: o.ease,
                stagger: o.stagger,
                overwrite: "auto",
                scrollTrigger: {
                    trigger: el,
                    start: o.start,
                    once: true,
                    invalidateOnRefresh: true,
                    refreshPriority: -1,
                },
            });
        });
    }


    function initNav() {
        const nav = $(".nav-primary--mobile");
        const toggle = $(".nav-toggle");
        if (!nav || !toggle) return;

        const setState = (isOpen) => {
            nav.classList.toggle("is-open", isOpen);
            toggle.classList.toggle("is-active", isOpen);
            doc.body.classList.toggle("menu-open", isOpen);
            toggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
        };

        toggle.addEventListener("click", () => {
            setState(!nav.classList.contains("is-open"));
        });

        initNav.close = () => setState(false);
    }

    function initSmoothScroll() {
        let activeTween = null;

        const aliasMap = {
            "#works": "#how-it-works",
        };

        function getHeaderOffset() {
            const header = $(".site-header");
            if (!header) return 0;
            const pos = window.getComputedStyle(header).position;
            if (pos !== "fixed" && pos !== "sticky") return 0;
            return Math.max(0, Math.round(header.getBoundingClientRect().height));
        }

        function parseHashFromLink(linkEl) {
            const rawHref = linkEl.getAttribute("href") || "";
            if (!rawHref || rawHref === "#") return null;

            if (rawHref.charAt(0) === "#") return rawHref;

            try {
                const url = new URL(rawHref, window.location.href);
                const samePage =
                    url.origin === window.location.origin &&
                    url.pathname === window.location.pathname;
                if (samePage && url.hash && url.hash !== "#") return url.hash;
            } catch (e) {}
            return null;
        }

        function resolveTarget(hash) {
            if (!hash) return null;

            const idFromHash = (() => {
                try {
                    return decodeURIComponent(hash.slice(1));
                } catch (e) {
                    return hash.slice(1);
                }
            })();
            if (!idFromHash) return null;

            let el = doc.getElementById(idFromHash);
            if (el) return {
                el,
                hash
            };

            const aliased = aliasMap[hash];
            if (!aliased) return null;
            const aliasedId = aliased.charAt(0) === "#" ? aliased.slice(1) : aliased;
            if (!aliasedId) return null;

            el = doc.getElementById(aliasedId);
            if (!el) return null;

            return {
                el,
                hash: aliased
            };
        }

        function focusTarget(target) {
            try {
                target.setAttribute("tabindex", "-1");
                target.focus({
                    preventScroll: true
                });
            } catch (e) {}
        }

        function scrollToTarget(target, hash) {
            const headerOffset = getHeaderOffset();
            const extraGap = 6;
            const y =
                window.pageYOffset +
                target.getBoundingClientRect().top -
                headerOffset -
                extraGap;

            if (!Motion.hasGsap() || Motion.reduced()) {
                window.scrollTo({
                    top: y,
                    behavior: Motion.reduced() ? "auto" : "smooth"
                });
                if (hash) history.replaceState(null, "", hash);
                focusTarget(target);
                return;
            }

            if (activeTween) activeTween.kill();

            const proxy = {
                y: window.pageYOffset
            };
            activeTween = window.gsap.to(proxy, {
                y,
                duration: 1.05,
                ease: "power3.out",
                onUpdate: () => window.scrollTo(0, proxy.y),
                onComplete: () => {
                    activeTween = null;
                    if (hash) history.replaceState(null, "", hash);
                    if (window.ScrollTrigger?.refresh) window.ScrollTrigger.refresh();
                    focusTarget(target);
                },
            });
        }

        doc.addEventListener("click", (e) => {
            const link = e.target && e.target.closest ? e.target.closest("a") : null;
            if (!link) return;

            const hash = parseHashFromLink(link);
            if (!hash) return;

            const resolved = resolveTarget(hash);
            if (!resolved) return;

            e.preventDefault();
            if (initNav.close) initNav.close();
            scrollToTarget(resolved.el, resolved.hash);
        });
    }

    function initBlogCategoryFilter() {
        const filtersRoot = $("[data-blog-filters]");
        if (!filtersRoot) return;

        const buttons = $$("[data-blog-filter]", filtersRoot);
        const mode = (filtersRoot.getAttribute("data-blog-filter-mode") || "client").toLowerCase();
        if (!buttons.length) return;

        if (mode === "server") {
            const url = new URL(window.location.href);
            const currentCategory = (url.searchParams.get("category") || "all").trim().toLowerCase();

            buttons.forEach((button) => {
                const buttonSlug = String(button.getAttribute("data-blog-filter") || "all").trim().toLowerCase();
                button.classList.toggle("is-active", buttonSlug === currentCategory);
            });

            buttons.forEach((button) => {
                button.addEventListener("click", (event) => {
                    event.preventDefault();
                    const slug = String(button.getAttribute("data-blog-filter") || "all").trim().toLowerCase();
                    const nextUrl = new URL(window.location.href);
                    if (slug === "all") nextUrl.searchParams.delete("category");
                    else nextUrl.searchParams.set("category", slug);
                    nextUrl.searchParams.delete("paged");
                    window.location.assign(nextUrl.toString());
                });
            });
            return;
        }

        const posts = $$("[data-blog-post]");
        const emptyState = $("[data-blog-empty]");
        if (!buttons.length || !posts.length) return;

        const setActiveFilter = (slug) => {
            const activeSlug = String(slug || "all").trim().toLowerCase();
            let visibleCount = 0;

            buttons.forEach((button) => {
                const buttonSlug = String(button.getAttribute("data-blog-filter") || "all").trim().toLowerCase();
                button.classList.toggle("is-active", buttonSlug === activeSlug);
            });

            posts.forEach((post) => {
                const categoryList = (post.getAttribute("data-blog-categories") || "")
                    .split(/\s+/)
                    .map((item) => item.trim().toLowerCase())
                    .filter(Boolean);

                const isVisible = activeSlug === "all" || categoryList.includes(activeSlug);
                post.hidden = !isVisible;
                post.style.display = isVisible ? "" : "none";
                if (isVisible) visibleCount += 1;
            });

            if (emptyState) {
                emptyState.hidden = visibleCount > 0;
            }
        };

        buttons.forEach((button) => {
            button.addEventListener("click", (event) => {
                event.preventDefault();
                setActiveFilter(button.getAttribute("data-blog-filter") || "all");
            });
        });

        setActiveFilter("all");
    }

    function initWebShare() {
        const shareButtons = $$("[data-web-share]");
        if (!shareButtons.length) return;

        shareButtons.forEach((button) => {
            button.addEventListener("click", async (event) => {
                event.preventDefault();

                const shareUrl = button.getAttribute("data-share-url") || window.location.href;
                const shareTitle = button.getAttribute("data-share-title") || doc.title || "";
                const payload = {
                    url: shareUrl,
                    title: shareTitle
                };

                if (navigator.share) {
                    try {
                        await navigator.share(payload);
                        return;
                    } catch (err) {
                        if (err && err.name === "AbortError") return;
                    }
                }

                if (navigator.clipboard && navigator.clipboard.writeText) {
                    try {
                        await navigator.clipboard.writeText(shareUrl);
                        return;
                    } catch (err) {}
                }

                window.open(shareUrl, "_blank", "noopener,noreferrer");
            });
        });
    }

    function initSinglePostTocSpy() {
        const content = $(".single-post__content");
        const tocLinks = $$(".single-post__toc-link");
        if (!content || !tocLinks.length) return;

        const headingById = new Map();
        const linkById = new Map();

        tocLinks.forEach((link) => {
            const href = link.getAttribute("href") || "";
            if (!href || href.charAt(0) !== "#") return;
            const id = href.slice(1);
            if (!id) return;

            const heading = content.querySelector("#" + CSS.escape(id));
            if (!heading) return;

            headingById.set(id, heading);
            linkById.set(id, link);
        });

        const ids = Array.from(headingById.keys());
        if (!ids.length) return;

        let currentActiveId = null;

        const setActive = (id) => {
            if (!id || !linkById.has(id) || currentActiveId === id) return;
            currentActiveId = id;
            linkById.forEach((link, key) => {
                link.classList.toggle("is-active", key === id);
            });
        };

        const updateByScrollPosition = () => {
            const triggerLine = window.innerHeight * 0.25;
            let nextActiveId = ids[0];

            for (let i = 0; i < ids.length; i++) {
                const id = ids[i];
                const heading = headingById.get(id);
                if (!heading) continue;
                if (heading.getBoundingClientRect().top <= triggerLine) nextActiveId = id;
                else break;
            }

            setActive(nextActiveId);
        };

        setActive(ids[0]);
        updateByScrollPosition();

        let ticking = false;
        const onScroll = () => {
            if (ticking) return;
            ticking = true;
            window.requestAnimationFrame(() => {
                updateByScrollPosition();
                ticking = false;
            });
        };

        window.addEventListener("scroll", onScroll, {
            passive: true
        });
        window.addEventListener("resize", onScroll);
        window.addEventListener("load", onScroll, {
            once: true
        });
    }

    function initCounters() {
        const counters = $$("[data-counter-start][data-counter-end]");
        if (!counters.length) return;

        const hasST = window.ScrollTrigger && window.gsap && (!window.Motion || Motion.hasST());
        const reduced = window.Motion && Motion.reduced && Motion.reduced();

        const formatValue = (value, decimals = 3) => "$" + value.toFixed(decimals).replace(".", ",");

        counters.forEach((el) => {
            const start = parseFloat(el.getAttribute("data-counter-start"));
            const end = parseFloat(el.getAttribute("data-counter-end"));
            const duration = parseInt(el.getAttribute("data-counter-duration") || "1200", 10);
            const delay = parseInt(el.getAttribute("data-counter-delay") || "0", 10);

            if (Number.isNaN(start) || Number.isNaN(end) || Number.isNaN(duration)) return;

            let played = false;

            const run = () => {
                if (played) return;
                played = true;

                const startTime = performance.now();
                const tick = (now) => {
                    const p = Math.min((now - startTime) / duration, 1);
                    const value = start + (end - start) * p;
                    el.textContent = formatValue(value);
                    if (p < 1) requestAnimationFrame(tick);
                    else el.textContent = formatValue(end);
                };
                requestAnimationFrame(tick);
            };

            const play = () => {
                if (delay > 0) setTimeout(run, delay);
                else run();
            };

            if (reduced) {
                el.textContent = formatValue(end);
                return;
            }

            if (hasST) {
                ScrollTrigger.create({
                    trigger: el,
                    start: "top 95%",
                    once: true,
                    invalidateOnRefresh: true,
                    onEnter: play,
                });
            } else {
                play();
            }
        });
    }

    function initHeroPhoneBgAnim() {
        const elements = $$(".hero__phone-bg, .hero__title, .hero__subtitle, .hero__form, .hero__blog-filters");
        if (!elements.length) return;

        if (!Motion.hasGsap() || Motion.reduced()) {
            elements.forEach(el => {
                el.style.opacity = "1";
                el.style.animation = "none";
            });
            return;
        }

        window.gsap.fromTo(
            elements, {
                y: 80,
                opacity: 0
            }, {
                y: 0,
                opacity: 1,
                duration: 1.2,
                ease: "power2.out",
                stagger: 0.12
            }
        );
    }

    function initRevealAnimations() {
        if (!Motion.hasST() || Motion.reduced()) return;

        const els = window.gsap.utils.toArray(".anim-reveal");
        if (!els.length) return;

        window.gsap.set(els, {
            y: 130,
            opacity: 0
        });

        els.forEach((el) => {
            window.gsap.to(el, {
                y: 0,
                opacity: 1,
                duration: 1,
                ease: "power2.out",
                scrollTrigger: {
                    trigger: el,
                    start: "top 95%",
                    once: true,
                    invalidateOnRefresh: true,
                },
            });
        });

        window.gsap.delayedCall(0.2, () => window.ScrollTrigger.refresh());
    }

    function initRevealAnimationsOpacity() {
        if (!Motion.hasST() || Motion.reduced()) return;

        const els = window.gsap.utils.toArray(".anim-reveal-opacity");
        if (!els.length) return;


        window.gsap.set(els, {
            y: 100,
            opacity: 0
        });

        els.forEach((el) => {
            window.gsap.to(el, {
                y: 0,
                opacity: 1,
                duration: 1,
                ease: "power2.out",
                scrollTrigger: {
                    trigger: el,
                    start: "top 99%",
                    end: "bottom 10%",
                    once: true,
                    invalidateOnRefresh: true,
                    refreshPriority: -1,
                    onRefresh: (self) => {
                        if (self.isActive || self.progress > 0) window.gsap.set(el, {
                            y: 0
                        });
                    },
                },
            });
        });
        window.gsap.delayedCall(0.2, () => window.ScrollTrigger.refresh());
        window.addEventListener("load", () => window.ScrollTrigger.refresh(), {
            once: true
        });
    }

    function initRevealAnimationsOpacityDown() {
        if (!Motion.hasST() || Motion.reduced()) return;

        const els = window.gsap.utils.toArray(".anim-reveal-opacity-down");
        if (!els.length) return;

        window.gsap.set(els, {
            y: -180,
            opacity: 0
        });

        els.forEach((el) => {
            const triggerEl = el.closest(".hero__phone-card-item") || el;

            window.gsap.to(el, {
                y: 0,
                opacity: 1,
                duration: 0.9,
                delay: 0.5,
                ease: "power3.out",
                scrollTrigger: {
                    trigger: triggerEl,
                    start: "top 99%",
                    end: "bottom 10%",
                    once: true,
                    invalidateOnRefresh: true,
                    refreshPriority: -1,
                    onRefresh: (self) => {
                        if (self.progress > 0) window.gsap.set(el, {
                            y: 0
                        });
                    },
                },
            });
        });
        window.gsap.delayedCall(0.2, () => window.ScrollTrigger.refresh());
        window.addEventListener("load", () => window.ScrollTrigger.refresh(), {
            once: true
        });
    }

    function initRevealAnimationsLow() {
        if (!Motion.hasST() || Motion.reduced()) return;

        const els = window.gsap.utils.toArray(".anim-reveal-low");
        if (!els.length) return;


        window.gsap.set(els, {
            y: 60,
            opacity: 1
        });

        els.forEach((el) => {
            window.gsap.to(el, {
                y: 0,
                opacity: 1,
                duration: 1,
                ease: "power2.out",
                scrollTrigger: {
                    trigger: el,
                    start: "top 90%",
                    end: "bottom 10%",
                    once: true,
                    invalidateOnRefresh: true,
                    refreshPriority: -1,
                    onRefresh: (self) => {
                        if (self.isActive || self.progress > 0) window.gsap.set(el, {
                            y: 0
                        });
                    },
                },
            });
        });

        window.gsap.delayedCall(0.2, () => window.ScrollTrigger.refresh());
        window.addEventListener("load", () => window.ScrollTrigger.refresh(), {
            once: true
        });
    }


    function initGsapScrollAnimations() {
        if (!Motion.hasST()) return false;

        const {
            gsap
        } = window;
        gsap.registerPlugin(window.ScrollTrigger);

        ScrollTrigger.config({
            ignoreMobileResize: true
        });

        if (window.MotionPathPlugin) gsap.registerPlugin(window.MotionPathPlugin);

        revealOnScroll(".anim-on-scroll");

		gsap.utils.toArray(".works__orbit").forEach((orbit) => {
			const path = orbit.querySelector(".works__orbit-path-line");
			const center = orbit.querySelector(".works__orbit-center");
			const icons = orbit.querySelectorAll(".works__orbit-icon");
			if (!path || !center || !icons.length || !window.MotionPathPlugin) return;
		
			const offsets = [0, 0.33, 0.66];
		
			gsap.set(orbit, {
				opacity: 0,
				y: 40
			});
		
			const tl = gsap.timeline({
				paused: true
			});
		
			tl.to(orbit, {
				opacity: 1,
				y: 0,
				duration: 1,
				ease: "power2.out",
			}, 0);
		
			tl.to(center, {
				y: -8,
				duration: 1.8,
				ease: "sine.inOut",
				yoyo: true,
				repeat: -1,
			}, 0.6);
		
			const tiltDeg = -20;
			const tiltRad = (-tiltDeg * Math.PI) / 180;
		
			function applyDepth(orbitEl, icon) {
				const orbitRect = orbitEl.getBoundingClientRect();
				const iconRect = icon.getBoundingClientRect();
		
				const centerY = orbitRect.top + orbitRect.height / 2;
				const centerX = orbitRect.left + orbitRect.width / 2;
		
				const iconCenterY = iconRect.top + iconRect.height / 2;
				const iconCenterX = iconRect.left + iconRect.width / 2;
		
				const dx = iconCenterX - centerX;
				const dy = iconCenterY - centerY;
		
				const yAligned = dx * Math.sin(tiltRad) + dy * Math.cos(tiltRad);
				const isBack = yAligned < 0;
		
				const t = Math.min(Math.abs(yAligned) / (orbitRect.height / 2), 1);
				const scale = isBack ? 1 - 0.12 * t : 1;
		
				gsap.set(icon, {
					zIndex: isBack ? 1 : 4,
					scale
				});
			}
		
			icons.forEach((icon, index) => {
				const start = offsets[index % offsets.length];
		
				gsap.set(icon, {
					motionPath: {
						path,
						align: path,
						alignOrigin: [0.5, 0.5],
						autoRotate: false,
						start,
						end: start,
					},
					transformOrigin: "50% 50%",
				});
		
				applyDepth(orbit, icon);
		
				tl.to(icon, {
					duration: 8,
					repeat: -1,
					ease: "none",
					motionPath: {
						path,
						align: path,
						alignOrigin: [0.5, 0.5],
						autoRotate: false,
						start,
						end: start + 1,
					},
					onUpdate: () => applyDepth(orbit, icon),
				}, 0.6);
			});
		
			const triggerEl =
				orbit.closest(".works__card") ||
				orbit.closest(".works-steps__item") ||
				orbit;
		
			let st = null;
		
			const play = () => {
				tl.restart(true);
				st && st.kill();
			};
		
			st = ScrollTrigger.create({
				trigger: triggerEl,
				start: REVEAL.start,
				once: true,
				invalidateOnRefresh: true,
				onEnter: play,
			});
		});

        gsap.utils.toArray(".works__card_one")
		.filter((card) => !card.classList.contains("works__card--activity"))
		.forEach((card) => {

			const phoneCard = card.querySelector(".hero__phone-card");
			const label = card.querySelector(".hero__phone-card-label");
			const value = card.querySelector(".hero__phone-card-value");
			const badge = card.querySelector(".hero__phone-card-badge");

			const items = card.querySelectorAll(".hero__phone-card-item");
			const itemIcons = card.querySelectorAll(".hero__phone-card-item img");

			const priceEls = card.querySelectorAll(".anim-price-reveal");


			const phoneGroup = [phoneCard, value, badge, ...items].filter(Boolean);
			if (phoneGroup.length) gsap.set(phoneGroup, {
				opacity: 0,
				y: 22
			});

			if (label) gsap.set(label, {
				opacity: 0,
				y: -14
			});
			if (itemIcons.length) gsap.set(itemIcons, {
				opacity: 0,
				y: -10
			});

			priceEls.forEach((el) => {
				const chars = el.querySelectorAll(".anim-char");
				if (!chars.length) return;
				gsap.set(chars, {
					opacity: 0,
					y: 40
				});
			});

			const tl = gsap.timeline({
				paused: true
			});


			if (phoneGroup.length) {
				tl.to(phoneGroup, {
					opacity: 1,
					y: 0,
					duration: 1.2,
					ease: "sine.out",
					stagger: 0.2,
				}, 0.2);
			}

			if (label) {
				tl.to(label, {
					opacity: 1,
					y: 0,
					duration: 0.5,
					ease: "sine.out",
				}, 0.85);
			}

			if (itemIcons.length) {
				tl.to(itemIcons, {
					opacity: 1,
					y: 0,
					duration: 0.55,
					ease: "sine.out",
					stagger: 0.1,
				}, 0.95);
			}

			priceEls.forEach((el) => {
				const chars = el.querySelectorAll(".anim-char");
				if (!chars.length) return;

				tl.to(chars, {
					opacity: 1,
					y: 0,
					duration: 0.9,
					ease: "power3.out",
					stagger: 0.3,
				}, 1.1);
			});

			let st = null;
			const play = () => {
				tl.restart(true);
				st && st.kill();
			};

			st = ScrollTrigger.create({
				trigger: card,
				start: REVEAL.start,
				once: true,
				invalidateOnRefresh: true,
				onEnter: play,
			});
		});

        gsap.utils.toArray(".works__card--activity").forEach((card) => {
            const phoneWrap = card.querySelector(".works__activity-phone");
            const phone = card.querySelector(".works__activity-ui");
            const title = card.querySelector(".works__activity-title");
            const tabs = card.querySelector(".works__activity-tabs");
            const icons = card.querySelectorAll(".works__activity-icons img");

            const portfolioTitle = card.querySelector(".works__activity-portfolio-title");
            const portfolioRow = card.querySelector(".works__activity-portfolio-row");
            const portfolioTrack = card.querySelector(".works__activity-portfolio-track");
            const portfolioItemsCards = card.querySelectorAll(".works__activity-portfolio-item");
            const filters = card.querySelectorAll(".works__activity-filter");
            const date = card.querySelector(".works__activity-date");

            if (!phone || !title || !tabs) return;

            if (phoneWrap) gsap.set(phoneWrap, {
                opacity: 0,
                y: 22
            });
            gsap.set(phone, {
                opacity: 0
            });
            gsap.set([title, tabs], {
                opacity: 0,
                y: 12
            });

            if (icons.length) gsap.set(icons, {
                opacity: 0,
                y: -12
            });

            const portfolioItems = [portfolioTitle, date].filter(Boolean);
            if (portfolioItems.length) gsap.set(portfolioItems, {
                opacity: 0,
                y: 12
            });

            if (filters.length) gsap.set(filters, {
                opacity: 0,
                y: 12
            });
            if (portfolioItemsCards.length) gsap.set(portfolioItemsCards, {
                opacity: 0,
                y: 12
            });

            if (portfolioTrack) gsap.set(portfolioTrack, {
                x: 0
            });

            const tl = gsap.timeline({
                paused: true
            });

            if (phoneWrap) {
                tl.to(phoneWrap, {
                    opacity: 1,
                    y: 0,
                    duration: 1.5,
                    ease: "power2.out"
                }, 0);
            }

            tl.to(phone, {
                    opacity: 1,
                    duration: 0.6,
                    ease: "power2.out"
                }, 0)
                .to([title, tabs], {
                    opacity: 1,
                    y: 0,
                    duration: 0.45,
                    ease: "power2.out"
                }, ">-0.1")
                .addLabel("iconsStart")
                .to(icons, {
                    opacity: 1,
                    y: 0,
                    duration: 0.4,
                    ease: "power2.out",
                    stagger: 0.08
                }, "iconsStart");

            if (portfolioTitle) {
                tl.to(portfolioTitle, {
                    opacity: 1,
                    y: 0,
                    duration: 0.4,
                    ease: "power2.out"
                }, "iconsStart+=0.1");
            }

            if (portfolioItemsCards.length) {
                tl.addLabel("cardsStart").to(
                    portfolioItemsCards, {
                        opacity: 1,
                        y: 0,
                        duration: 0.4,
                        ease: "power2.out",
                        stagger: 0.08
                    },
                    "cardsStart"
                );
            }

            if (filters.length) {
                tl.to(filters, {
                    opacity: 1,
                    y: 0,
                    duration: 0.4,
                    ease: "power2.out",
                    stagger: 0.08
                }, "cardsStart+=0.1");
            }

            if (date) {
                tl.to(date, {
                    opacity: 1,
                    y: 0,
                    duration: 0.4,
                    ease: "power2.out"
                }, ">-0.05");
            }

            if (portfolioTrack && portfolioRow) {
                const calcShift = () => {
                    gsap.set(portfolioTrack, {
                        x: 0
                    });

                    const items = portfolioTrack.querySelectorAll(".works__activity-portfolio-item");
                    const target = items[3];
                    if (!target) return 0;

                    const padR = parseFloat(getComputedStyle(portfolioRow).paddingRight) || 0;

                    const rowRect = portfolioRow.getBoundingClientRect();
                    const targetRect = target.getBoundingClientRect();

                    const visibleRight = rowRect.right - padR;
                    const extraOffset = 7;
                    const fudge = 35;

                    return Math.max(targetRect.right - visibleRight + extraOffset + fudge, 0);
                };

                tl.to(portfolioTrack, {
                        x: () => -calcShift(),
                        duration: 1.1,
                        ease: "power2.inOut"
                    })
                    .to(portfolioTrack, {
                        x: 0,
                        duration: 1.1,
                        ease: "power2.inOut"
                    });
            }

            let st = null;
            const play = () => {
                tl.restart(true);
                st && st.kill();
            };

            st = window.ScrollTrigger.create({
                trigger: card,
                start: REVEAL.start,
                once: true,
                invalidateOnRefresh: true,
                onEnter: play,
            });
        });

		gsap.utils.toArray(".works-steps__item--two").forEach((item) => {
			const counter = item.querySelector(".works-steps__step-two-counter");
			const button =
				item.querySelector(".works-steps__step-two-control--plus") ||
				item.querySelector(".works-steps__step-two-control-icon");
			if (!counter || !button) return;
		
			const value = {
				num: 1
			};
		
			const tl = gsap.timeline({
				paused: true
			});
		
			tl.fromTo(
				value, {
					num: 1
				}, {
					num: 10,
					duration: 2.5,
					ease: "none",
					onUpdate: () => {
						counter.textContent = Math.round(value.num) + "%";
					},
				},
				0
			).fromTo(
				button, {
					scale: 1
				}, {
					scale: 1.2,
					duration: 0.5,
					ease: "sine.inOut",
					repeat: 4,
					yoyo: true,
					onComplete: () => gsap.set(button, {
						scale: 1
					}),
				},
				0
			);
		
			ScrollTrigger.create({
				trigger: item,
				start: REVEAL.start,
				once: true,
				invalidateOnRefresh: true,
				onEnter: () => tl.restart(true),
			});
		});

        gsap.utils.toArray(".works-steps__three-orbit-center").forEach((center) => {
            const stepItem = center.closest(".works-steps__item--three") || center;

            const state = {
                angle: 0
            };
            const radius = 14;

            const orbitTween = gsap.to(state, {
                angle: Math.PI * 2,
                duration: 4,
                ease: "none",
                repeat: -1,
                paused: true,
                onUpdate: () => {
                    const x = Math.cos(state.angle) * radius;
                    const y = Math.sin(state.angle) * radius;
                    center.style.transform =
                        "translate(-50%, -50%) translate(" + x + "px, " + y + "px)";
                },
            });

            window.ScrollTrigger.create({
                trigger: stepItem,
                start: "top 100%",
                once: true,
                invalidateOnRefresh: true,
                onEnter: () => orbitTween.play(),
            });
        });

        gsap.utils.toArray(".works-steps__item--four").forEach((item) => {
            const viewport = item.querySelector(".works-steps__four-anim-cards");
            const track = item.querySelector(".works-steps__four-anim-cards-track");
            const icons = Array.from(item.querySelectorAll(".works-steps__four-anim-icon"));
            if (!viewport || !track) return;

            let cardsTween = null;

            const reset = () => {
                if (cardsTween) {
                    cardsTween.kill();
                    cardsTween = null;
                }
                gsap.set(track, {
                    y: 0
                });
                if (icons.length) gsap.set(icons, {
                    x: 0,
                    y: 0
                });
            };

            const play = () => {
                reset();

                const card = track.querySelector(".works-steps__four-anim-card");
                if (!card) return;

                const gap = parseFloat(getComputedStyle(track).gap) || 0;
                const cardH = card.getBoundingClientRect().height;

                const visibleCount = 3;
                const innerH = cardH * visibleCount + gap * (visibleCount - 1);
                const edgePad = Math.round(Math.min(24, Math.max(12, cardH * 0.18))) - 5;

                gsap.set(viewport, {
                    height: innerH + edgePad * 2
                });
                gsap.set(track, {
                    paddingTop: edgePad,
                    paddingBottom: edgePad,
                    y: 0
                });

                const maxY = Math.max(track.scrollHeight - viewport.clientHeight, 0);
                if (maxY > 0) {
                    cardsTween = gsap.to(track, {
                        y: -maxY,
                        duration: 6.5,
                        ease: "power1.inOut",
                        repeat: -1,
                        yoyo: true,
                        repeatDelay: 0.8
                    });
                }
            };

            window.ScrollTrigger.create({
                trigger: item,
                start: REVEAL.start,
                end: "bottom top",
                invalidateOnRefresh: true,
                onEnter: play,
                onEnterBack: play,
                onLeave: reset,
                onLeaveBack: reset,
            });
        });

        gsap.delayedCall(0.2, () => ScrollTrigger.refresh());
        window.addEventListener("load", () => ScrollTrigger.refresh(), {
            once: true
        });
        window.addEventListener("orientationchange", () => ScrollTrigger.refresh());

        return true;
    }

    function initScrollAnimationsFallback() {
        const targets = $$(".anim-on-scroll");
        if (!targets.length) return;

        const obs = createOnceObserver({
            threshold: 0.2,
            rootMargin: "0px 0px -10% 0px"
        }, (el, observer) => {
            el.classList.add("is-animated");
            observer.unobserve(el);
        });

        targets.forEach((el) => obs.observe(el));
    }

    function initCssScrollAnimations() {
        const elements = $$('[class*="anim-"]');
        if (!elements.length) return;

        const eligible = elements.filter((el) => {
            if (el.closest(".hero")) return false;
            if (el.classList.contains("anim-on-scroll")) return false;
            if (el.classList.contains("anim-price-reveal")) return false;

            return Array.from(el.classList).some((cls) => cls.indexOf("anim-") === 0 && cls.indexOf("anim-delay-") !== 0);
        });

        if (!eligible.length) return;

        eligible.forEach((el) => {
            el.style.animationPlayState = "paused";
        });

        const obs = createOnceObserver({
            threshold: 0.2,
            rootMargin: "0px 0px -10% 0px"
        }, (el, observer) => {
            el.style.animationPlayState = "running";
            observer.unobserve(el);
        });

        eligible.forEach((el) => obs.observe(el));
    }

    function initStepsFlowLine() {
        const flows = $$(".works-steps__flow");
        if (!flows.length) return;

        const obs = createOnceObserver({
            threshold: 0.2,
            rootMargin: "0px 0px -10% 0px"
        }, (el) => {
            el.classList.remove("is-animated");
            void el.offsetHeight;
            el.classList.add("is-animated");
        });

        flows.forEach((el) => obs.observe(el));
    }
			
    function initWaitlistTracking() {
        const forms = $$(".wpcf7-form");
        if (!forms.length) return;

        function getCookie(name) {
            const m = document.cookie.match("(^|;)\\s*" + name + "\\s*=\\s*([^;]+)");
            return m ? m.pop() : "";
        }

        const TRACKING_FIELDS = [
            "sv_fbc",
            "sv_fbp",
            "sv_utm_source",
            "sv_utm_medium",
            "sv_utm_campaign",
            "sv_utm_content",
            "sv_event_id",
        ];

        function ensureHiddenInputs(form) {
            TRACKING_FIELDS.forEach((name) => {
                if (form.querySelector('input[name="' + name + '"]')) return;
                const input = doc.createElement("input");
                input.type = "hidden";
                input.name = name;
                input.value = "";
                form.appendChild(input);
            });
        }

        function setValue(form, name, value) {
            const input = form.querySelector('input[name="' + name + '"]');
            if (input) input.value = value;
        }

        function populate(form) {
            const params = new URLSearchParams(window.location.search);

            const eventID =
                "lead_" +
                Date.now() +
                "_" +
                Math.random().toString(36).substr(2, 9);

            try {
                sessionStorage.setItem("sv_event_id", eventID);
            } catch (e) {}

            setValue(form, "sv_fbc", getCookie("_fbc"));
            setValue(form, "sv_fbp", getCookie("_fbp"));
            setValue(form, "sv_utm_source", params.get("utm_source") || "");
            setValue(form, "sv_utm_medium", params.get("utm_medium") || "");
            setValue(form, "sv_utm_campaign", params.get("utm_campaign") || "");
            setValue(form, "sv_utm_content", params.get("utm_content") || "");
            setValue(form, "sv_event_id", eventID);
        }

        forms.forEach((form) => {
            ensureHiddenInputs(form);
            populate(form);
            form.addEventListener(
                "submit",
                () => populate(form),
                true
            );
        });
    }			

    function initContactFormSuccess() {
        doc.addEventListener("wpcf7mailsent", (event) => {
            const form = event && event.target;
            if (!form || !form.closest) return;

            const wrapper = form.closest(".contact__form");
            if (!wrapper) return;

            const fromData = (wrapper.getAttribute("data-thank-you-url") || "").trim();
            const fromTheme = window.spendvestTheme && window.spendvestTheme.thankYouUrl ?
                String(window.spendvestTheme.thankYouUrl).trim() :
                "";
            const url = fromData || fromTheme;

            if (url) {
                window.location.assign(url);
            }
        }, false);
    }

    function initPlaceholdersOnFocus() {
        doc.addEventListener("focusin", (e) => {
            const el = e.target;
            if (!el || !el.getAttribute || !el.matches) return;
            if (!el.matches("input[placeholder], textarea[placeholder]")) return;

            const ph = el.getAttribute("placeholder");
            if (!ph || !el.dataset) return;

            if (!el.dataset.ph) el.dataset.ph = ph;
            el.setAttribute("placeholder", "");
        }, false);

        doc.addEventListener("focusout", (e) => {
            const el = e.target;
            if (!el || !el.getAttribute || !el.matches) return;
            if (!el.matches("input[placeholder], textarea[placeholder]")) return;
            if (!el.dataset || !el.dataset.ph) return;

            const hasValue = typeof el.value === "string" && el.value.trim().length > 0;
            if (!hasValue) el.setAttribute("placeholder", el.dataset.ph);
        }, false);
    }


    function initDlLanding() {
        const root = $("#dl-landing");
        if (!root || !root.getAttribute) return;

        const openAppUrl = (root.getAttribute("data-open-app-url") || "").trim();
        const autoOpen = root.getAttribute("data-auto-open") === "1";

        if (autoOpen && openAppUrl) {
            window.location.href = openAppUrl;
            return;
        }

        $$("[data-dl-store]", root).forEach((link) => {
            const href = (link.getAttribute("href") || "").trim();
            if (href === "#" || href === "") {
                link.addEventListener("click", (e) => {
                    e.preventDefault();
                });
            }
        });
    }

    function initFaqAccordion() {
        const items = $$(".faq__item");
        if (!items.length) return;

        const section = $("#faq.faq") || items[0].closest(".faq");
        const list = section && section.querySelector(".faq__list");
        const triggerEl = list || section || items[0];

        if (Motion.hasST() && !Motion.reduced()) {
            const section = $("#faq.faq") || items[0].closest(".faq");
            const triggerEl = section?.querySelector(".faq__list") || section || items[0];

            window.gsap.to(items, {
                opacity: 1,
                y: 0,
                duration: 1,
                ease: "power2.out",
                stagger: 0.14,
                scrollTrigger: {
                    trigger: triggerEl,
                    start: "top 60%",
                    once: true,
                },
            });

            window.ScrollTrigger.refresh();
        }


        function setItemState(item, isOpen) {
            const btn = item.querySelector(".faq__question");
            const icon = item.querySelector(".faq__icon");

            item.classList.toggle("is-open", isOpen);
            if (btn) btn.setAttribute("aria-expanded", isOpen ? "true" : "false");
            if (icon) icon.textContent = isOpen ? "−" : "+";
        }

        items.forEach((item) => {
            const answer = item.querySelector(".faq__answer");
            if (item.classList.contains("is-open") && answer) answer.style.height = "auto";
        });

        items.forEach((item) => {
            const btn = item.querySelector(".faq__question");
            if (!btn) return;

            btn.addEventListener("click", () => {
                const isOpen = item.classList.contains("is-open");
                items.forEach((other) => setItemState(other, !isOpen && other === item));
            });
        });
    }



    onReady(() => {
        initNav();
        initSmoothScroll();
        initWebShare();
        initSinglePostTocSpy();
        initBlogCategoryFilter();
        initHeroPhoneBgAnim();
        initRevealAnimations();
        initRevealAnimationsOpacity();
        initRevealAnimationsOpacityDown();
        initRevealAnimationsLow()
        initCounters();
        initPriceReveal();

        if (!initGsapScrollAnimations()) {
            initScrollAnimationsFallback();
        }

        initCssScrollAnimations();
        initStepsFlowLine();
		initWaitlistTracking();
        initContactFormSuccess();
        initPlaceholdersOnFocus();
        initFaqAccordion();
        initDlLanding();
    });
})();