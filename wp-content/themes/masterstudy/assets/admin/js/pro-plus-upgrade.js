// Countdown Timer
function startCountdown(cycleDays) {
  const daysEl = document.getElementById('days');
  const hoursEl = document.getElementById('hours');
  const minutesEl = document.getElementById('minutes');
  const secondsEl = document.getElementById('seconds');

  if (!daysEl || !hoursEl || !minutesEl || !secondsEl) {
    return;
  }

  const cycleDuration = cycleDays * 24 * 60 * 60 * 1000;
  const storageKey = 'upgrade_countdown_end';

  let endTime = localStorage.getItem(storageKey);

  if (!endTime || Date.now() > parseInt(endTime, 10)) {
    endTime = Date.now() + cycleDuration;
    localStorage.setItem(storageKey, endTime);
  } else {
    endTime = parseInt(endTime, 10);
  }

  function updateTimer() {
    const now = Date.now();
    let remaining = endTime - now;

    if (remaining <= 0) {
      endTime = Date.now() + cycleDuration;
      localStorage.setItem(storageKey, endTime);
      remaining = endTime - Date.now();
    }

    const totalSeconds = Math.floor(remaining / 1000);
    const days = Math.floor(totalSeconds / (3600 * 24));
    const hours = Math.floor((totalSeconds % (3600 * 24)) / 3600);
    const minutes = Math.floor((totalSeconds % 3600) / 60);
    const seconds = totalSeconds % 60;

    daysEl.textContent = String(days).padStart(2, '0');
    hoursEl.textContent = String(hours).padStart(2, '0');
    minutesEl.textContent = String(minutes).padStart(2, '0');
    secondsEl.textContent = String(seconds).padStart(2, '0');
  }

  updateTimer();
  setInterval(updateTimer, 1000);
}

document.addEventListener('DOMContentLoaded', () => {
  startCountdown(10);
});

document.addEventListener('DOMContentLoaded', () => {
  startCountdown(1);
});

// FAQ Accordion
const faqItems = document.querySelectorAll('.faq-item');

faqItems.forEach(item => {
  item.querySelector('.faq-question').addEventListener('click', () => {
    if (item.classList.contains('active')) {
      item.classList.remove('active');
    } else {
      faqItems.forEach(i => i.classList.remove('active'));
      item.classList.add('active');
    }
  });
});

document.addEventListener('DOMContentLoaded', function () {
  // Feature Cards Scroll Animation
  if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;

  gsap.registerPlugin(ScrollTrigger);

  const cards = document.querySelectorAll('.feature-card-wrap');

  cards.forEach((card, index) => {
    gsap.set(card, {
      opacity: 1,
    });
  });

  cards.forEach((card, i) => {
    gsap.to(card, {
      opacity: 1,
      duration: 0,
      ease: 'power2.out',
      scrollTrigger: {
        trigger: card,
        start: 'top top',
        end: 'bottom top',
        scrub: true,
        onEnter: () => {
          gsap.to(card, {
            opacity: 0.3,
            duration: 1
          });
        },
        onLeaveBack: () => {
          gsap.to(card, {
            opacity: 1,
            duration: 1
          });
        }
      }
    });

    ScrollTrigger.create({
      trigger: card,
      start: 'center center',
      pin: true,
      pinSpacing: false,
      anticipatePin: 1,
      scrub: true,
    });

    if (i < cards.length - 1) {
      const nextCard = cards[i + 1];

      ScrollTrigger.create({
        trigger: nextCard,
        start: 'center center',
        onEnter: () => {
          card.style.display = 'none';
          card.classList.add('hidden');
        },
        onLeaveBack: () => {
          card.style.display = 'block';
          card.classList.remove('hidden');
        },
      });
    }
  });

  // Form elements
  const form = document.getElementById('masterstudy-wizard-form');
  const messageBlock = document.querySelector('.masterstudy-upgrade-message');
  const loadingStep = document.getElementById('step-loading');
  const formStep = document.getElementById('step-form');
  const progressBar = document.querySelector('.progress-bar-fill');
  const progressValue = document.getElementById('progress-value');

  if (!form) return;

  let currentPercent = 0;
  let progressInterval;

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    const emailInput = form.querySelector('#upgrade-email');
    const agreeCheckbox = form.querySelector('input[name="agree"]');
    const email = emailInput.value.trim();

    if (messageBlock) {
      messageBlock.textContent = '';
      messageBlock.style.color = '';
    }

    if (!email || !agreeCheckbox.checked) {
      messageBlock.textContent = 'Please enter a valid email and accept the agreement.';
      messageBlock.style.color = '#ef4444';
      return;
    }

    formStep.style.display = 'none';
    loadingStep.style.display = 'block';

    // Start progress simulation
    updateProgress(0);
    startProgressSimulation();

    fetch(masterstidy_theme_pro_plus_upgrade.ajax_url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
      },
      body: new URLSearchParams({
        action: 'register_trial_user',
        email: email,
        _ajax_nonce: masterstidy_theme_pro_plus_upgrade.nonce,
      }),
    })
    .then(response => {
      // Stop simulation and show real progress
      stopProgressSimulation();
      updateProgress(80);
      return response.json();
    })
    .then(data => {
      updateProgress(90);
      
      if (data.success) {
        updateProgress(100);
        
        // Check page availability before redirect
        checkPageAvailability('admin.php?page=stm-lms-license', () => {
          window.location.href = 'admin.php?page=stm-lms-license';
        });
      } else {
        showError(data.data || 'An error occurred during installation.');
      }
    })
    .catch(err => {
      stopProgressSimulation();
      showError('Network error. Please try again later.');
    });
  });

  function startProgressSimulation() {
    currentPercent = 0;
    progressInterval = setInterval(() => {
      if (currentPercent < 70) { // Max 70% before response
        currentPercent += 10;
        updateProgress(currentPercent);
      }
    }, 5000); // Every 5 seconds
  }

  function stopProgressSimulation() {
    if (progressInterval) {
      clearInterval(progressInterval);
      progressInterval = null;
    }
  }

  function showError(message) {
    stopProgressSimulation();
    loadingStep.style.display = 'none';
    formStep.style.display = 'block';
    progressBar.style.width = '0%';
    progressValue.textContent = '0';
    messageBlock.textContent = message;
    messageBlock.style.color = '#ef4444';
  }

  function updateProgress(percent) {
    progressBar.style.width = `${percent}%`;
    progressValue.textContent = percent;
  }
});

// Reset plugin buttons
document.querySelector('.reset-pro-plus-plugin')?.addEventListener('click', async (e) => {
  e.preventDefault();

  const statusLicenseBlock = document.getElementById('step-status-license');
  const proFeaturesBlock = document.getElementById('step-pro-features');

  if (statusLicenseBlock && proFeaturesBlock) {
    statusLicenseBlock.style.display = 'none';
    proFeaturesBlock.style.display = 'flex';
  }
});

document.querySelector('.reset-pro-plus-plugin-cancel')?.addEventListener('click', (e) => {
  e.preventDefault();

  const statusLicenseBlock = document.getElementById('step-status-license');
  const proFeaturesBlock = document.getElementById('step-pro-features');
  
  if (statusLicenseBlock && proFeaturesBlock) {
    proFeaturesBlock.style.display = 'none';
    statusLicenseBlock.style.display = 'flex'
  }
});

document.querySelector('#step-pro-features .reset-pro-plus-plugin')?.addEventListener('click', async (e) => {
  e.preventDefault();

  const proFeaturesBlock = document.getElementById('step-pro-features');
  const rollBackBlock = document.getElementById('step-roll-back');
  
  if (proFeaturesBlock && rollBackBlock) {
    proFeaturesBlock.style.display = 'none';
    rollBackBlock.style.display = 'block';
  }

  // Start progress simulation for PRO plugin installation
  updatePluginProgress(0);
  startProPluginProgressSimulation();

  try {
    const response = await fetch(masterstidy_theme_pro_plus_upgrade.ajax_url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: new URLSearchParams({
        action: 'stm_upgrade_pro_plugin',
        nonce: masterstidy_theme_pro_plus_upgrade.nonce,
      }),
    });

    // Stop simulation and show real progress
    stopProPluginProgressSimulation();
    updatePluginProgress(80);

    const data = await response.json();

    if (data.success) {
      updatePluginProgress(90);
      completePluginDownloadProgress().then(() => {
        updatePluginProgress(100);
        window.location.href = 'admin.php?page=stm-lms-settings';
      });
    } else {
      showPluginDownloadError(data.data || 'Update failed');
    }
  } catch (err) {
    stopProPluginProgressSimulation();
    showPluginDownloadError('Network error. Please try again later.');
  }
});

function startProPluginProgressSimulation() {
  const progressBar = document.querySelector('#step-roll-back .progress-bar-fill');
  const progressValue = document.querySelector('#step-roll-back #progress-value');
  
  if (!progressBar || !progressValue) return;

  let currentPercent = 0;

  const interval = setInterval(() => {
    if (currentPercent < 70) { // Max 70% before response
      currentPercent += 10;
      updatePluginProgress(currentPercent);
    }
  }, 5000); // Every 5 seconds

  window.proPluginProgressInterval = interval;
}

function stopProPluginProgressSimulation() {
  if (window.proPluginProgressInterval) {
    clearInterval(window.proPluginProgressInterval);
    window.proPluginProgressInterval = null;
  }
}

function startPluginDownloadProgress() {
  const progressBar = document.querySelector('#step-roll-back .progress-bar-fill');
  const progressValue = document.querySelector('#step-roll-back #progress-value');
  
  if (!progressBar || !progressValue) return;

  let currentPercent = 0;

  const interval = setInterval(() => {
    if (currentPercent >= 65) {
      clearInterval(interval);
      return;
    }
    currentPercent += 3;
    updatePluginProgress(currentPercent);
  }, 50);

  window.pluginDownloadInterval = interval;
}

function completePluginDownloadProgress() {
  return new Promise((resolve) => {
    const progressBar = document.querySelector('#step-roll-back .progress-bar-fill');
    const progressValue = document.querySelector('#step-roll-back #progress-value');
    
    if (!progressBar || !progressValue) {
      resolve();
      return;
    }

    if (window.pluginDownloadInterval) {
      clearInterval(window.pluginDownloadInterval);
    }

    let currentPercent = parseInt(progressValue.textContent) || 65;

    const interval = setInterval(() => {
      currentPercent += 7;
      if (currentPercent >= 100) {
        clearInterval(interval);
        updatePluginProgress(100);
        resolve();
      } else {
        updatePluginProgress(currentPercent);
      }
    }, 30);
  });
}

function showPluginDownloadError(message) {
  if (window.pluginDownloadInterval) {
    clearInterval(window.pluginDownloadInterval);
  }
  
  if (window.proPluginProgressInterval) {
    clearInterval(window.proPluginProgressInterval);
  }

  const rollBackBlock = document.getElementById('step-roll-back');
  const proFeaturesBlock = document.getElementById('step-pro-features');
  
  if (rollBackBlock && proFeaturesBlock) {
    rollBackBlock.style.display = 'none';
    proFeaturesBlock.style.display = 'block';
  }

  const progressBar = document.querySelector('#step-roll-back .progress-bar-fill');
  const progressValue = document.querySelector('#step-roll-back #progress-value');
  
  if (progressBar && progressValue) {
    progressBar.style.width = '0%';
    progressValue.textContent = '0';
  }

  alert('Error: ' + message);
}

function updatePluginProgress(percent) {
  const progressBar = document.querySelector('#step-roll-back .progress-bar-fill');
  const progressValue = document.querySelector('#step-roll-back #progress-value');
  
  if (progressBar && progressValue) {
    progressBar.style.width = `${percent}%`;
    progressValue.textContent = percent;
  }
}

function checkPageAvailability(url, onSuccess, maxAttempts = 10) {
  let attempts = 0;

  function tryPage() {
    attempts++;
    
    fetch(url, {
      method: 'HEAD',
      credentials: 'same-origin'
    })
    .then(response => {
      if (response.ok) {
        sessionStorage.setItem('masterstudy_installation_complete', 'true');
        onSuccess();
      } else {
        throw new Error('Page not available');
      }
    })
    .catch(error => {
      if (attempts < maxAttempts) {
        setTimeout(tryPage, 1000);
      } else {
        sessionStorage.setItem('masterstudy_installation_complete', 'true');
        setTimeout(onSuccess, 2000);
      }
    });
  }

  setTimeout(tryPage, 2000);
}

// Check for confetti trigger on stm-lms-license page
document.addEventListener('DOMContentLoaded', function() {
  const currentPage = window.location.search.includes('page=stm-lms-license')
  const installationComplete = sessionStorage.getItem('masterstudy_installation_complete');
  
  if (currentPage && installationComplete === 'true') {
    window.addEventListener('load', function() {
      setTimeout(function() {
        if (typeof window.confettiManager !== 'undefined') {
          window.confettiManager.addConfetti();
          sessionStorage.removeItem('masterstudy_installation_complete');
        }
      }, 500);
    });
  }
});

(() => {
  "use strict";

  const Utils = {
    parsePx: (value) => parseFloat(value.replace(/px/, "")),

    getRandomInRange: (min, max, precision = 0) => {
      const multiplier = Math.pow(10, precision);
      const randomValue = Math.random() * (max - min) + min;
      return Math.floor(randomValue * multiplier) / multiplier;
    },

    getRandomItem: (array) => array[Math.floor(Math.random() * array.length)],

    getScaleFactor: () => Math.log(window.innerWidth) / Math.log(1920),

    debounce: (func, delay) => {
      let timeout;
      return (...args) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => func(...args), delay);
      };
    },
  };

  const DEG_TO_RAD = Math.PI / 180;

  const defaultConfettiConfig = {
    confettiesNumber: 250,
    confettiRadius: 6,
    confettiColors: [
      "#fcf403", "#62fc03", "#f4fc03", "#03e7fc", "#03fca5", "#a503fc", "#fc03ad", "#fc03c2"
    ],
    emojies: [],
    svgIcon: null,
  };

  class Confetti {
    constructor({ initialPosition, direction, radius, colors, emojis, svgIcon }) {
      const speedFactor = Utils.getRandomInRange(0.9, 1.7, 3) * Utils.getScaleFactor();
      this.speed = { x: speedFactor, y: speedFactor };
      this.finalSpeedX = Utils.getRandomInRange(0.2, 0.6, 3);
      this.rotationSpeed = emojis.length || svgIcon ? 0.01 : Utils.getRandomInRange(0.03, 0.07, 3) * Utils.getScaleFactor();
      this.dragCoefficient = Utils.getRandomInRange(0.0005, 0.0009, 6);
      this.radius = { x: radius, y: radius };
      this.initialRadius = radius;
      this.rotationAngle = direction === "left" ? Utils.getRandomInRange(0, 0.2, 3) : Utils.getRandomInRange(-0.2, 0, 3);
      this.emojiRotationAngle = Utils.getRandomInRange(0, 2 * Math.PI);
      this.radiusYDirection = "down";

      const angle = direction === "left" ? Utils.getRandomInRange(82, 15) * DEG_TO_RAD : Utils.getRandomInRange(-15, -82) * DEG_TO_RAD;
      this.absCos = Math.abs(Math.cos(angle));
      this.absSin = Math.abs(Math.sin(angle));

      const offset = Utils.getRandomInRange(-150, 0);
      const position = {
        x: initialPosition.x + (direction === "left" ? -offset : offset) * this.absCos,
        y: initialPosition.y - offset * this.absSin
      };

      this.position = { ...position };
      this.initialPosition = { ...position };
      this.color = emojis.length || svgIcon ? null : Utils.getRandomItem(colors);
      this.emoji = emojis.length ? Utils.getRandomItem(emojis) : null;
      this.svgIcon = null;

      if (svgIcon) {
        this.svgImage = new Image();
        this.svgImage.src = svgIcon;
        this.svgImage.onload = () => {
          this.svgIcon = this.svgImage;
        };
      }

      this.createdAt = Date.now();
      this.direction = direction;
    }

    draw(context) {
      const { x, y } = this.position;
      const { x: radiusX, y: radiusY } = this.radius;
      const scale = window.devicePixelRatio;

      if (this.svgIcon) {
        context.save();
        context.translate(scale * x, scale * y);
        context.rotate(this.emojiRotationAngle);
        context.drawImage(this.svgIcon, -radiusX, -radiusY, radiusX * 2, radiusY * 2);
        context.restore();
      } else if (this.color) {
        context.fillStyle = this.color;
        context.beginPath();
        context.ellipse(x * scale, y * scale, radiusX * scale, radiusY * scale, this.rotationAngle, 0, 2 * Math.PI);
        context.fill();
      } else if (this.emoji) {
        context.font = `${radiusX * scale}px serif`;
        context.save();
        context.translate(scale * x, scale * y);
        context.rotate(this.emojiRotationAngle);
        context.textAlign = "center";
        context.fillText(this.emoji, 0, radiusY / 2);
        context.restore();
      }
    }

    updatePosition(deltaTime, currentTime) {
      const elapsed = currentTime - this.createdAt;

      if (this.speed.x > this.finalSpeedX) {
        this.speed.x -= this.dragCoefficient * deltaTime;
      }

      this.position.x += this.speed.x * (this.direction === "left" ? -this.absCos : this.absCos) * deltaTime;
      this.position.y = this.initialPosition.y - this.speed.y * this.absSin * elapsed + 0.00125 * Math.pow(elapsed, 2) / 2;

      if (!this.emoji && !this.svgIcon) {
        this.rotationSpeed -= 1e-5 * deltaTime;
        this.rotationSpeed = Math.max(this.rotationSpeed, 0);

        if (this.radiusYDirection === "down") {
          this.radius.y -= deltaTime * this.rotationSpeed;
          if (this.radius.y <= 0) {
            this.radius.y = 0;
            this.radiusYDirection = "up";
          }
        } else {
          this.radius.y += deltaTime * this.rotationSpeed;
          if (this.radius.y >= this.initialRadius) {
            this.radius.y = this.initialRadius;
            this.radiusYDirection = "down";
          }
        }
      }
    }

    isVisible(canvasHeight) {
      return this.position.y < canvasHeight + 100;
    }
  }

  class ConfettiManager {
    constructor() {
      this.canvas = document.createElement("canvas");
      this.canvas.style = "position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 1000; pointer-events: none;";
      document.body.appendChild(this.canvas);
      this.context = this.canvas.getContext("2d");
      this.confetti = [];
      this.lastUpdated = Date.now();
      window.addEventListener("resize", Utils.debounce(() => this.resizeCanvas(), 200));
      this.resizeCanvas();
      requestAnimationFrame(() => this.loop());
    }

    resizeCanvas() {
      this.canvas.width = window.innerWidth * window.devicePixelRatio;
      this.canvas.height = window.innerHeight * window.devicePixelRatio;
    }

    addConfetti(config = {}) {
      const { confettiesNumber, confettiRadius, confettiColors, emojies, svgIcon } = {
        ...defaultConfettiConfig,
        ...config,
      };

      const baseY = (5 * window.innerHeight) / 7;
      for (let i = 0; i < confettiesNumber / 2; i++) {
        this.confetti.push(new Confetti({
          initialPosition: { x: 0, y: baseY },
          direction: "right",
          radius: confettiRadius,
          colors: confettiColors,
          emojis: emojies,
          svgIcon,
        }));
        this.confetti.push(new Confetti({
          initialPosition: { x: window.innerWidth, y: baseY },
          direction: "left",
          radius: confettiRadius,
          colors: confettiColors,
          emojis: emojies,
          svgIcon,
        }));
      }
    }

    resetAndStart(config = {}) {
      this.confetti = [];
      this.addConfetti(config);
    }

    loop() {
      const currentTime = Date.now();
      const deltaTime = currentTime - this.lastUpdated;
      this.lastUpdated = currentTime;

      this.context.clearRect(0, 0, this.canvas.width, this.canvas.height);

      this.confetti = this.confetti.filter((item) => {
        item.updatePosition(deltaTime, currentTime);
        item.draw(this.context);
        return item.isVisible(this.canvas.height);
      });

      requestAnimationFrame(() => this.loop());
    }
  }

  window.confettiManager = new ConfettiManager();

  window.showSuccessScreen = function() {
    document.getElementById('step-loading').style.display = 'none';
    document.getElementById('step-success').style.display = 'block';
    window.confettiManager.addConfetti();
  };

  window.triggerConfetti = function() {
    window.confettiManager.addConfetti();
  };
})();

document.addEventListener('DOMContentLoaded', function() {
  if (typeof masterstidy_theme_pro_plus_upgrade !== 'undefined' && masterstidy_theme_pro_plus_upgrade.activation_pending) {
    fetch(masterstidy_theme_pro_plus_upgrade.ajax_url, {
      method: 'POST',
      headers: {
          'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
      },
      body: new URLSearchParams({
          action: 'stm_activate_trial_license_ajax',
          nonce: masterstidy_theme_pro_plus_upgrade.nonce
      })
    })
    .then(response => response.json());
  }
});

// Tracking clicks on Freemius license activation buttons
document.addEventListener('DOMContentLoaded', function() {
  // Track click on license activation button
  document.addEventListener('click', function(e) {
    const target = e.target;

    // Check various activation button selectors
    if (target.matches('.button-activate-license, .fs-activate-license, [data-action="activate-license"]') ||
        target.closest('.button-activate-license, .fs-activate-license, [data-action="activate-license"]') ||
        // Добавляем проверку для кнопки в #fs_connect .fs-actions
        (target.closest('#fs_connect .fs-actions') && target.tagName === 'BUTTON')) {

      // Send AJAX to delete option
      if (typeof masterstidy_theme_pro_plus_upgrade !== 'undefined') {
        fetch(masterstidy_theme_pro_plus_upgrade.ajax_url, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
          },
          body: new URLSearchParams({
            action: 'stm_delete_license_data',
            nonce: masterstidy_theme_pro_plus_upgrade.nonce
          })
        })
        .then(response => response.json())
        .then(data => {
        })
        .catch(error => {
        });
      }
    }
  });
});
