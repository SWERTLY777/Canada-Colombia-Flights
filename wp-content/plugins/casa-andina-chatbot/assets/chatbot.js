(function () {
  'use strict';

  var config = window.CasaAndinaChatbot;
  var root = document.querySelector('[data-ca-chatbot]');

  if (!config || !root) {
    return;
  }

  var strings = config.strings;
  var panel = root.querySelector('[data-ca-panel]');
  var launcher = root.querySelector('[data-ca-launcher]');
  var launcherText = root.querySelector('[data-ca-launcher-text]');
  var closeButton = root.querySelector('[data-ca-close]');
  var messages = root.querySelector('[data-ca-messages]');
  var quick = root.querySelector('[data-ca-quick]');
  var form = root.querySelector('[data-ca-form]');
  var input = root.querySelector('[data-ca-input]');
  var sendButton = root.querySelector('[data-ca-send]');
  var contact = root.querySelector('[data-ca-contact]');
  var history = [];
  var busy = false;
  var greeted = false;

  panel.id = 'ca-chatbot-panel';
  root.querySelector('[data-ca-title]').textContent = strings.assistantName;
  root.querySelector('[data-ca-status]').textContent = strings.status;
  root.querySelector('[data-ca-privacy]').textContent = strings.privacy;
  root.querySelector('[data-ca-input-label]').textContent = strings.placeholder;
  launcherText.textContent = strings.assistantName;
  launcher.setAttribute('aria-label', strings.openLabel);
  closeButton.setAttribute('aria-label', strings.closeLabel);
  input.setAttribute('placeholder', strings.placeholder);
  sendButton.textContent = strings.send;
  contact.textContent = strings.contact;
  contact.href = config.contactUrl;

  function addMessage(role, text, extraClass) {
    var row = document.createElement('div');
    var bubble = document.createElement('div');

    row.className = 'ca-chatbot__message ca-chatbot__message--' + role;
    if (extraClass) {
      row.classList.add(extraClass);
    }

    bubble.className = 'ca-chatbot__bubble';
    bubble.textContent = text;
    row.appendChild(bubble);
    messages.appendChild(row);
    messages.scrollTop = messages.scrollHeight;

    return row;
  }

  function renderQuickReplies() {
    quick.innerHTML = '';
    strings.quickReplies.forEach(function (label) {
      var button = document.createElement('button');
      button.type = 'button';
      button.textContent = label;
      button.addEventListener('click', function () {
        submitMessage(label);
      });
      quick.appendChild(button);
    });
  }

  function openChat() {
    panel.hidden = false;
    panel.setAttribute('aria-hidden', 'false');
    launcher.setAttribute('aria-expanded', 'true');
    launcher.setAttribute('aria-label', strings.closeLabel);
    root.classList.add('is-open');

    if (!greeted) {
      addMessage('assistant', strings.welcome);
      renderQuickReplies();
      greeted = true;
    }

    window.setTimeout(function () {
      input.focus();
    }, 120);
  }

  function closeChat() {
    panel.setAttribute('aria-hidden', 'true');
    panel.hidden = true;
    launcher.setAttribute('aria-expanded', 'false');
    launcher.setAttribute('aria-label', strings.openLabel);
    root.classList.remove('is-open');
    launcher.focus();
  }

  function setBusy(state) {
    busy = state;
    input.disabled = state;
    sendButton.disabled = state;
    quick.querySelectorAll('button').forEach(function (button) {
      button.disabled = state;
    });
    root.classList.toggle('is-busy', state);
  }

  function submitMessage(rawMessage) {
    var message = (rawMessage || input.value || '').trim();
    if (!message || busy) {
      if (!message) {
        input.setCustomValidity(strings.empty);
        input.reportValidity();
        window.setTimeout(function () { input.setCustomValidity(''); }, 1000);
      }
      return;
    }

    var previousHistory = history.slice(-8);
    addMessage('user', message);
    history.push({ role: 'user', content: message });
    input.value = '';
    input.style.height = '';
    quick.innerHTML = '';
    setBusy(true);

    var typing = addMessage('assistant', strings.typing, 'ca-chatbot__message--typing');
    var body = new URLSearchParams();
    body.set('action', 'ca_chatbot_message');
    body.set('nonce', config.nonce);
    body.set('lang', config.lang);
    body.set('message', message);
    body.set('history', JSON.stringify(previousHistory));

    fetch(config.ajaxUrl, {
      method: 'POST',
      credentials: 'same-origin',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
      body: body.toString()
    })
      .then(function (response) {
        return response.json().catch(function () {
          throw new Error('invalid_response');
        });
      })
      .then(function (payload) {
        typing.remove();
        if (!payload.success) {
          throw new Error(payload.data && payload.data.message ? payload.data.message : strings.error);
        }

        var reply = payload.data && payload.data.reply ? payload.data.reply : strings.error;
        addMessage('assistant', reply);
        history.push({ role: 'assistant', content: reply });
        history = history.slice(-10);
      })
      .catch(function (error) {
        typing.remove();
        var fallback = error && error.message && error.message !== 'invalid_response' ? error.message : strings.error;
        addMessage('assistant', fallback, 'ca-chatbot__message--error');
      })
      .finally(function () {
        setBusy(false);
        input.focus();
      });
  }

  launcher.addEventListener('click', function () {
    if (panel.hidden) {
      openChat();
    } else {
      closeChat();
    }
  });

  closeButton.addEventListener('click', closeChat);

  form.addEventListener('submit', function (event) {
    event.preventDefault();
    submitMessage();
  });

  input.addEventListener('keydown', function (event) {
    if (event.key === 'Enter' && !event.shiftKey) {
      event.preventDefault();
      submitMessage();
    }
  });

  input.addEventListener('input', function () {
    input.style.height = 'auto';
    input.style.height = Math.min(input.scrollHeight, 112) + 'px';
  });

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape' && !panel.hidden) {
      closeChat();
    }
  });
})();
