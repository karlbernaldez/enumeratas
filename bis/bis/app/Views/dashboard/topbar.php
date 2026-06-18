<!-- Floating Chat Widget -->
<div class="cw-wrap" id="cwWrap">

    <!-- Bubble toggle -->
    <button class="cw-toggle" id="cwToggle" onclick="cwOpen()" aria-label="Open chat">
        <span class="cw-toggle-icon" id="cwIcon">
            <i class="fas fa-comment-dots"></i>
        </span>
        <span class="cw-unread" id="cwUnread">1</span>
    </button>

    <!-- Panel -->
    <div class="cw-panel" id="cwPanel">

        <!-- Header -->
        <div class="cw-header">
            <div class="cw-header-left">
                <div class="cw-header-avatar">
                    <i class="fas fa-robot"></i>
                    <span class="cw-header-dot"></span>
                </div>
                <div class="cw-header-text">
                    <span class="cw-header-name">BIS Assistant</span>
                    <span class="cw-header-sub">Bacolod Barangay · Online</span>
                </div>
            </div>

            <div class="cw-header-actions">
                <button class="cw-hbtn" title="Minimize" onclick="cwOpen()">
                    <i class="fas fa-minus"></i>
                </button>
                <button class="cw-hbtn" title="Close" onclick="cwClose()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <!-- Date divider -->
        <div class="cw-date-divider">
            <span>Today</span>
        </div>

        <!-- Messages -->
        <div class="cw-messages" id="cwMessages">
            <div class="cw-row cw-row--bot">
                <div class="cw-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="cw-body">
                    <div class="cw-bubble">
                        Hello! I'm the <strong>BIS Assistant</strong> 👋<br>
                        I can help with barangay inquiries, document requirements, and resident information update requests.
                    </div>
                    <span class="cw-ts">Just now</span>
                </div>
            </div>

            <!-- Quick chips -->
            <div class="cw-chips" id="cwChips">
                <button class="cw-chip" onclick="cwQuick('How do I request a barangay clearance?')">
                    <i class="fas fa-file-alt"></i> Request clearance
                </button>

                <button class="cw-chip" onclick="cwQuick('What are the requirements for certificate of residency?')">
                    <i class="fas fa-home"></i> Residency certificate
                </button>

                <button class="cw-chip" onclick="cwQuick('How do I update my census information?')">
                    <i class="fas fa-users"></i> Census update
                </button>

                <button class="cw-chip" onclick="cwQuick('May bagong panganak po sa household namin')">
                    <i class="fas fa-baby"></i> Newborn update
                </button>

                <button class="cw-chip" onclick="cwQuick('Namatay na po ang isang member ng household namin')">
                    <i class="fas fa-user-minus"></i> Death record
                </button>

                <button class="cw-chip" onclick="cwQuick('What are the barangay office hours?')">
                    <i class="fas fa-clock"></i> Office hours
                </button>
            </div>
        </div>

        <!-- Input -->
        <div class="cw-footer">
            <div class="cw-input-wrap">
                <input
                    type="text"
                    id="cwInput"
                    class="cw-input"
                    placeholder="Type a message..."
                    onkeydown="if(event.key === 'Enter') cwSend();"
                >

                <button class="cw-send" onclick="cwSend()" aria-label="Send message">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>

            <p class="cw-powered">
                Powered by <strong>Bacolod BIS</strong>
            </p>
        </div>

    </div>
</div>

<script>
    (function () {
        function now() {
            const d = new Date();

            return d.getHours().toString().padStart(2, '0') +
                ':' +
                d.getMinutes().toString().padStart(2, '0');
        }

        function escapeHtml(value) {
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function formatMessage(value) {
            return escapeHtml(value).replace(/\n/g, '<br>');
        }

        function addMsg(text, isUser) {
            const wrap = document.getElementById('cwMessages');
            const chips = document.getElementById('cwChips');

            if (!wrap) {
                return;
            }

            if (chips) {
                chips.remove();
            }

            const row = document.createElement('div');
            row.className = 'cw-row ' + (isUser ? 'cw-row--user' : 'cw-row--bot');

            const safeText = formatMessage(text);

            if (isUser) {
                row.innerHTML = `
                    <div class="cw-body">
                        <div class="cw-bubble">${safeText}</div>
                        <span class="cw-ts">${now()}</span>
                    </div>
                `;
            } else {
                row.innerHTML = `
                    <div class="cw-avatar">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div class="cw-body">
                        <div class="cw-bubble">${safeText}</div>
                        <span class="cw-ts">${now()}</span>
                    </div>
                `;
            }

            wrap.appendChild(row);
            wrap.scrollTop = wrap.scrollHeight;
        }

        function typing() {
            const wrap = document.getElementById('cwMessages');

            if (!wrap) {
                return;
            }

            const existingTyping = document.getElementById('cwTyping');

            if (existingTyping) {
                existingTyping.remove();
            }

            const row = document.createElement('div');
            row.className = 'cw-row cw-row--bot cw-typing-row';
            row.id = 'cwTyping';

            row.innerHTML = `
                <div class="cw-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="cw-body">
                    <div class="cw-bubble cw-typing">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            `;

            wrap.appendChild(row);
            wrap.scrollTop = wrap.scrollHeight;
        }

        function removeTyping() {
            const typingRow = document.getElementById('cwTyping');

            if (typingRow) {
                typingRow.remove();
            }
        }

        async function askBackend(message) {
            const headers = {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            };

            <?php if (function_exists('csrf_hash')): ?>
                headers['X-CSRF-TOKEN'] = '<?= csrf_hash() ?>';
            <?php endif; ?>

            const response = await fetch('/chatbot/ask', {
                method: 'POST',
                headers: headers,
                body: JSON.stringify({
                    message: message
                })
            });

            if (!response.ok) {
                throw new Error('Chatbot request failed with status ' + response.status);
            }

            return await response.json();
        }

        async function handleMessage(message) {
            message = String(message || '').trim();

            if (!message) {
                return;
            }

            addMsg(message, true);

            const unread = document.getElementById('cwUnread');

            if (unread) {
                unread.style.display = 'none';
            }

            typing();

            try {
                const data = await askBackend(message);

                removeTyping();

                const reply = data.reply || 'Sorry, I could not process your message right now.';

                addMsg(reply, false);
            } catch (error) {
                console.error(error);

                removeTyping();

                addMsg(
                    'Sorry, the chatbot is currently unavailable. Please try again later or contact the barangay office.',
                    false
                );
            }
        }

        window.cwSend = function () {
            const input = document.getElementById('cwInput');

            if (!input) {
                return;
            }

            const message = input.value.trim();

            if (!message) {
                return;
            }

            input.value = '';

            handleMessage(message);
        };

        window.cwQuick = function (message) {
            const chips = document.getElementById('cwChips');

            if (chips) {
                chips.remove();
            }

            handleMessage(message);
        };

        window.cwOpen = function () {
            const panel = document.getElementById('cwPanel');
            const wrap = document.getElementById('cwWrap');
            const unread = document.getElementById('cwUnread');

            if (!panel || !wrap) {
                return;
            }

            panel.classList.toggle('cw-open');
            wrap.classList.toggle('cw-active');

            if (panel.classList.contains('cw-open') && unread) {
                unread.style.display = 'none';
            }
        };

        window.cwClose = function () {
            const panel = document.getElementById('cwPanel');
            const wrap = document.getElementById('cwWrap');

            if (panel) {
                panel.classList.remove('cw-open');
            }

            if (wrap) {
                wrap.classList.remove('cw-active');
            }
        };

        // Legacy aliases used by chatbot topic cards in dashboard pages.
        window.toggleChat = window.cwOpen;
        window.sendQuick = window.cwQuick;
    })();
</script>