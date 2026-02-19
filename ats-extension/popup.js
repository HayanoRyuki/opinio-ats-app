/**
 * Opinio ATS - Popup Settings Script
 */

document.addEventListener('DOMContentLoaded', () => {
  const atsUrlInput = document.getElementById('atsUrl');
  const companyIdInput = document.getElementById('companyId');
  const apiKeyInput = document.getElementById('apiKey');
  const saveBtn = document.getElementById('saveBtn');
  const testBtn = document.getElementById('testBtn');
  const saveResult = document.getElementById('saveResult');
  const testResult = document.getElementById('testResult');

  // 保存済み設定を読み込み
  chrome.storage.sync.get(
    ['atsUrl', 'companyId', 'apiKey'],
    (data) => {
      atsUrlInput.value = data.atsUrl || 'https://ats.opinio.co.jp';
      companyIdInput.value = data.companyId || '';
      apiKeyInput.value = data.apiKey || '';
    }
  );

  // 設定保存
  saveBtn.addEventListener('click', () => {
    const settings = {
      atsUrl: atsUrlInput.value.replace(/\/+$/, ''), // 末尾スラッシュ除去
      companyId: companyIdInput.value.trim(),
      apiKey: apiKeyInput.value.trim(),
    };

    if (!settings.companyId) {
      showResult(saveResult, '会社IDを入力してください', 'error');
      return;
    }

    chrome.storage.sync.set(settings, () => {
      showResult(saveResult, '設定を保存しました', 'success');
    });
  });

  // 接続テスト
  testBtn.addEventListener('click', async () => {
    const atsUrl = atsUrlInput.value.replace(/\/+$/, '');
    const apiKey = apiKeyInput.value.trim();

    if (!atsUrl) {
      showResult(testResult, 'ATS URLを入力してください', 'error');
      return;
    }

    testBtn.disabled = true;
    testBtn.textContent = 'テスト中...';
    testResult.style.display = 'none';

    try {
      const headers = {
        'Content-Type': 'application/json',
        Accept: 'application/json',
      };
      if (apiKey) {
        headers['X-API-Key'] = apiKey;
      }

      // ヘルスチェック的にAPIを叩く（GETで404でもサーバー応答があればOK）
      const response = await fetch(`${atsUrl}/api/intake/scout`, {
        method: 'OPTIONS',
        headers,
      });

      // OPTIONSが405やCORSエラーでも、fetchが成功すればサーバーは生きている
      showResult(testResult, `接続成功（ステータス: ${response.status}）`, 'success');
    } catch (err) {
      // ネットワークエラーやCORSエラー
      // CORSエラーの場合でもサーバーは動いている可能性がある
      if (err.message.includes('Failed to fetch')) {
        showResult(
          testResult,
          'サーバーに接続できません。URLを確認してください。',
          'error'
        );
      } else {
        showResult(testResult, `エラー: ${err.message}`, 'error');
      }
    } finally {
      testBtn.disabled = false;
      testBtn.innerHTML = `
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
          <polyline points="22 4 12 14.01 9 11.01"/>
        </svg>
        接続テスト
      `;
    }
  });

  function showResult(element, message, type) {
    element.textContent = message;
    element.className = `${element.id === 'testResult' ? 'test-result' : 'save-result'} ${type}`;
    element.style.display = 'block';
    setTimeout(() => {
      element.style.display = 'none';
    }, 4000);
  }
});
