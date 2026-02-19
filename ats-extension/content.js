/**
 * Opinio ATS - BizReach Content Script
 *
 * ビズリーチの候補者ページを検知し、候補者情報を抽出して
 * ATSに送信するフローティングUIを表示する。
 *
 * セレクタはプレースホルダー。実際のBizReachアカウントで確認後に調整すること。
 */

(function () {
  'use strict';

  // ======================================================================
  // 1. BizReach ページ判定
  // ======================================================================

  /**
   * 候補者詳細ページかどうかを判定
   * TODO: 実際のBizReach URLパターンに合わせて調整
   */
  function isCandidatePage() {
    const url = window.location.href;
    const patterns = [
      /bizreach\.(jp|biz)\/.*candidate/i,
      /bizreach\.(jp|biz)\/.*profile/i,
      /bizreach\.(jp|biz)\/.*applicant/i,
      /bizreach\.(jp|biz)\/.*resume/i,
      /bizreach\.(jp|biz)\/.*scout.*detail/i,
    ];
    return patterns.some((p) => p.test(url));
  }

  // ======================================================================
  // 2. DOM セレクタ定義（プレースホルダー）
  // ======================================================================

  /**
   * BizReach の DOM セレクタ定義
   * 各フィールドに対して複数の候補セレクタを定義。
   * 上から順に試行し、最初にマッチしたものを使用。
   *
   * !! 重要: これらはプレースホルダーです !!
   * 実際のBizReachアカウントでDevToolsを使って正しいセレクタに置き換えてください。
   */
  const SELECTORS = {
    name: [
      // 候補者名
      '.candidate-name',
      '.profile-name',
      '[data-testid="candidate-name"]',
      'h1.name',
      '.resume-header .name',
      '.applicant-name',
    ],
    currentCompany: [
      // 現在の勤務先
      '.current-company',
      '.company-name',
      '[data-testid="current-company"]',
      '.resume-summary .company',
    ],
    currentPosition: [
      // 現在の役職
      '.current-position',
      '.job-title',
      '[data-testid="current-position"]',
      '.resume-summary .position',
    ],
    experience: [
      // 経験年数
      '.experience-years',
      '.career-years',
      '[data-testid="experience"]',
    ],
    age: [
      // 年齢
      '.candidate-age',
      '.age',
      '[data-testid="age"]',
    ],
    education: [
      // 学歴
      '.education',
      '.school-name',
      '[data-testid="education"]',
      '.resume-education .school',
    ],
    skills: [
      // スキル・資格
      '.skills-list',
      '.skill-tags',
      '[data-testid="skills"]',
    ],
    annualIncome: [
      // 希望年収
      '.annual-income',
      '.salary',
      '[data-testid="annual-income"]',
    ],
    location: [
      // 勤務地希望
      '.preferred-location',
      '.location',
      '[data-testid="location"]',
    ],
  };

  // ======================================================================
  // 3. データ抽出
  // ======================================================================

  /**
   * セレクタリストから最初にマッチする要素のテキストを取得
   */
  function extractText(selectorList) {
    for (const selector of selectorList) {
      const el = document.querySelector(selector);
      if (el && el.textContent.trim()) {
        return el.textContent.trim();
      }
    }
    return null;
  }

  /**
   * ページから候補者データを抽出
   */
  function extractCandidateData() {
    const data = {
      name: extractText(SELECTORS.name),
      profileUrl: window.location.href,
      profile: {},
    };

    // プロフィール情報を抽出
    const profileFields = {
      current_company: SELECTORS.currentCompany,
      current_position: SELECTORS.currentPosition,
      experience: SELECTORS.experience,
      age: SELECTORS.age,
      education: SELECTORS.education,
      skills: SELECTORS.skills,
      annual_income: SELECTORS.annualIncome,
      location: SELECTORS.location,
    };

    for (const [key, selectors] of Object.entries(profileFields)) {
      const value = extractText(selectors);
      if (value) {
        data.profile[key] = value;
      }
    }

    return data;
  }

  // ======================================================================
  // 4. UI コンポーネント
  // ======================================================================

  /**
   * トースト通知を表示
   */
  function showToast(message, type = 'info') {
    let toast = document.getElementById('opinio-ats-toast');
    if (!toast) {
      toast = document.createElement('div');
      toast.id = 'opinio-ats-toast';
      document.body.appendChild(toast);
    }

    toast.textContent = message;
    toast.className = type;

    // アニメーション
    requestAnimationFrame(() => {
      toast.classList.add('show');
      setTimeout(() => {
        toast.classList.remove('show');
      }, 3000);
    });
  }

  /**
   * フィールド表示用HTML生成
   */
  function fieldHtml(label, value) {
    const displayValue = value || '（未検出）';
    const cls = value ? '' : ' empty';
    return `
      <div class="opinio-field">
        <div class="opinio-field-label">${label}</div>
        <div class="opinio-field-value${cls}">${displayValue}</div>
      </div>
    `;
  }

  /**
   * メインUIを作成・挿入
   */
  function createUI(candidateData) {
    // 既存パネルがあれば削除
    const existing = document.getElementById('opinio-ats-panel');
    if (existing) existing.remove();

    const panel = document.createElement('div');
    panel.id = 'opinio-ats-panel';

    const hasName = !!candidateData.name;
    const profileSummary = Object.entries(candidateData.profile)
      .map(([key, val]) => {
        const labels = {
          current_company: '現在の勤務先',
          current_position: '役職',
          experience: '経験年数',
          age: '年齢',
          education: '学歴',
          skills: 'スキル',
          annual_income: '希望年収',
          location: '勤務地',
        };
        return fieldHtml(labels[key] || key, val);
      })
      .join('');

    panel.innerHTML = `
      <!-- Floating Action Button -->
      <button id="opinio-ats-fab" title="Opinio ATS に送信">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/>
        </svg>
      </button>

      <!-- Card -->
      <div id="opinio-ats-card">
        <div class="opinio-card-header">
          <h3>Opinio ATS</h3>
          <span style="font-size:11px; opacity:0.8;">BizReach</span>
        </div>
        <div class="opinio-card-body">
          ${fieldHtml('候補者名', candidateData.name)}
          ${profileSummary}
          ${
            !hasName
              ? '<div class="opinio-status error">候補者情報を検出できませんでした。<br>セレクタの設定を確認してください。</div>'
              : ''
          }
          <button
            class="opinio-send-btn"
            id="opinio-ats-send"
            ${!hasName ? 'disabled' : ''}
          >
            ATSに送信
          </button>
          <div id="opinio-ats-status"></div>
        </div>
      </div>
    `;

    document.body.appendChild(panel);

    // FABクリックでカード開閉
    const fab = document.getElementById('opinio-ats-fab');
    const card = document.getElementById('opinio-ats-card');
    fab.addEventListener('click', () => {
      card.classList.toggle('open');
    });

    // 送信ボタン
    const sendBtn = document.getElementById('opinio-ats-send');
    sendBtn.addEventListener('click', () => {
      sendToAts(candidateData, sendBtn);
    });
  }

  // ======================================================================
  // 5. API 送信
  // ======================================================================

  /**
   * Background Script 経由でATSにデータを送信
   */
  async function sendToAts(candidateData, button) {
    button.disabled = true;
    button.textContent = '送信中...';

    const statusEl = document.getElementById('opinio-ats-status');
    statusEl.textContent = '';
    statusEl.className = 'opinio-status';

    try {
      const response = await chrome.runtime.sendMessage({
        action: 'sendToAts',
        data: candidateData,
      });

      if (response.success) {
        button.textContent = '送信完了';
        button.classList.add('success');
        statusEl.textContent = '取り込み管理にドラフトが追加されました';
        statusEl.classList.add('success');
        showToast('ATSに送信しました', 'success');
      } else {
        throw new Error(response.error || '送信に失敗しました');
      }
    } catch (err) {
      button.textContent = '再送信';
      button.disabled = false;
      button.classList.add('error');
      statusEl.textContent = err.message;
      statusEl.classList.add('error');
      showToast('送信エラー: ' + err.message, 'error');
    }
  }

  // ======================================================================
  // 6. 初期化
  // ======================================================================

  function init() {
    // 候補者ページかどうか判定
    if (!isCandidatePage()) {
      return;
    }

    // 設定を確認
    chrome.storage.sync.get(
      ['atsUrl', 'companyId', 'apiKey'],
      (settings) => {
        if (!settings.companyId) {
          showToast(
            'Opinio ATS: 設定が未完了です。拡張アイコンをクリックして設定してください。',
            'error'
          );
          return;
        }

        // データ抽出
        const candidateData = extractCandidateData();

        // UI表示
        createUI(candidateData);

        if (candidateData.name) {
          showToast(
            `候補者「${candidateData.name}」を検出しました`,
            'info'
          );
        }
      }
    );
  }

  // ページ読み込み完了後に初期化
  // SPAの場合はURLの変化も監視
  init();

  // SPA対応: URL変化を監視
  let lastUrl = location.href;
  const observer = new MutationObserver(() => {
    if (location.href !== lastUrl) {
      lastUrl = location.href;
      setTimeout(init, 1000); // DOMの更新を待つ
    }
  });
  observer.observe(document.body, { childList: true, subtree: true });
})();
