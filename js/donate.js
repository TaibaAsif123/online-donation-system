/* ============================================================
   Online Donation System — client-side validation for donate.php
   Part 2 (Member 2 — Eiman Asmat)

   This is convenience only. Every rule below is repeated in PHP
   inside donate.php, which is what actually protects the database.
   ============================================================ */

document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("donationForm");
  if (!form) return;

  const nameInput   = document.getElementById("donor_name");
  const emailInput  = document.getElementById("donor_email");
  const amountInput = document.getElementById("amount");
  const methodInput = document.getElementById("payment_method");
  const causeInput  = document.getElementById("cause_id");
  const messageBox  = document.getElementById("message");
  const msgCount    = document.getElementById("msgCount");
  const chips       = document.querySelectorAll(".chip[data-amount]");

  const sumCause  = document.getElementById("sumCause");
  const sumAmount = document.getElementById("sumAmount");
  const sumMethod = document.getElementById("sumMethod");

  const MIN_AMOUNT = 100;
  const MAX_AMOUNT = 1000000;
  const NAME_RE    = /^[A-Za-zÀ-ɏ][A-Za-zÀ-ɏ\s.'-]*$/;
  const EMAIL_RE   = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

  /* ---------- helpers ---------- */

  function fieldOf(input) {
    return input.closest(".field");
  }

  function setError(input, message) {
    const field = fieldOf(input);
    const slot  = document.getElementById("err-" + input.id);
    if (!field || !slot) return;

    if (message) {
      field.classList.add("has-error");
      slot.textContent = message;
      input.setAttribute("aria-invalid", "true");
    } else {
      field.classList.remove("has-error");
      slot.textContent = "";
      input.removeAttribute("aria-invalid");
    }
    return !message;
  }

  function money(value) {
    return "Rs. " + Number(value).toLocaleString("en-PK", {
      minimumFractionDigits: 0,
      maximumFractionDigits: 2
    });
  }

  /* ---------- per-field rules ---------- */

  function checkName() {
    const v = nameInput.value.trim();
    if (v === "")            return setError(nameInput, "Please enter your full name.");
    if (v.length < 3)        return setError(nameInput, "Name must be at least 3 characters.");
    if (v.length > 100)      return setError(nameInput, "Name cannot be longer than 100 characters.");
    if (!NAME_RE.test(v))    return setError(nameInput, "Name can only contain letters, spaces, apostrophes, hyphens and dots.");
    return setError(nameInput, "");
  }

  function checkEmail() {
    const v = emailInput.value.trim();
    if (v === "")            return setError(emailInput, "Please enter your email address.");
    if (!EMAIL_RE.test(v))   return setError(emailInput, "That does not look like a valid email address.");
    if (v.length > 150)      return setError(emailInput, "Email cannot be longer than 150 characters.");
    return setError(emailInput, "");
  }

  function checkAmount() {
    const raw = amountInput.value.trim();
    if (raw === "")                    return setError(amountInput, "Please enter a donation amount.");
    const n = Number(raw);
    if (Number.isNaN(n))               return setError(amountInput, "Amount must be a number.");
    if (n < MIN_AMOUNT)                return setError(amountInput, "The minimum donation is Rs. 100.");
    if (n > MAX_AMOUNT)                return setError(amountInput, "For donations above Rs. 1,000,000 please contact us directly.");
    return setError(amountInput, "");
  }

  function checkMethod() {
    if (methodInput.value === "") return setError(methodInput, "Please select a payment method.");
    return setError(methodInput, "");
  }

  function checkMessage() {
    if (!messageBox) return true;
    if (messageBox.value.length > 300) return setError(messageBox, "Message cannot be longer than 300 characters.");
    return setError(messageBox, "");
  }

  /* ---------- live summary in the sidebar ---------- */

  function updateSummary() {
    if (sumCause && causeInput) {
      sumCause.textContent = causeInput.options[causeInput.selectedIndex].text.trim();
    }
    if (sumAmount) {
      const n = Number(amountInput.value);
      sumAmount.textContent = amountInput.value.trim() !== "" && !Number.isNaN(n) && n > 0 ? money(n) : "—";
    }
    if (sumMethod) {
      sumMethod.textContent = methodInput.value !== "" ? methodInput.value : "—";
    }
  }

  /* ---------- preset amount chips ---------- */

  function syncChips() {
    chips.forEach(chip => {
      chip.classList.toggle("is-selected", chip.dataset.amount === amountInput.value.trim());
    });
  }

  chips.forEach(chip => {
    chip.addEventListener("click", function () {
      amountInput.value = chip.dataset.amount;
      syncChips();
      checkAmount();
      updateSummary();
      amountInput.focus();
    });
  });

  /* ---------- wiring ---------- */

  // Validate on blur, then keep correcting as they type once a field has errored.
  [[nameInput, checkName], [emailInput, checkEmail],
   [amountInput, checkAmount], [methodInput, checkMethod]].forEach(([input, check]) => {
    input.addEventListener("blur", check);
    input.addEventListener("input", function () {
      if (fieldOf(input).classList.contains("has-error")) check();
    });
  });

  methodInput.addEventListener("change", function () { checkMethod(); updateSummary(); });
  causeInput.addEventListener("change", updateSummary);
  amountInput.addEventListener("input", function () { syncChips(); updateSummary(); });

  if (messageBox && msgCount) {
    const countChars = function () { msgCount.textContent = messageBox.value.length; };
    messageBox.addEventListener("input", function () { countChars(); checkMessage(); });
    countChars();
  }

  /* ---------- submit ---------- */

  form.addEventListener("submit", function (event) {
    const checks = [checkName(), checkEmail(), checkAmount(), checkMethod(), checkMessage()];

    if (checks.includes(false)) {
      event.preventDefault();
      const firstBad = form.querySelector(".field.has-error .input");
      if (firstBad) {
        firstBad.focus();
        firstBad.scrollIntoView({ behavior: "smooth", block: "center" });
      }
      return;
    }

    // Stop double-submits while the page posts.
    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.textContent = "Processing…";
    }
  });

  syncChips();
  updateSummary();
});
