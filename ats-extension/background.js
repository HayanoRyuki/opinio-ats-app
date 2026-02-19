/**
 * Opinio ATS - Background Service Worker
 *
 * Content Script からのメッセージを受け取り、
 * ATS の /api/intake/scout エンドポイントに POST する。
 */

chrome.runtime.onMessage.addListener((request, sender, sendResponse) => {
  if (request.action === 'sendToAts') {
    handleSendToAts(request.data)
      .then((result) => sendResponse(result))
      .catch((err) => sendResponse({ success: false, error: err.message }));
    return true; // 非同期レスポンスを有効にする
  }
});

/**
 * ATSにスカウトデータを送信
 */
async function handleSendToAts(candidateData) {
  // 設定を取得
  const settings = await chrome.storage.sync.get([
    'atsUrl',
    'companyId',
    'apiKey',
  ]);

  const atsUrl = settings.atsUrl || 'https://ats.opinio.co.jp';
  const companyId = settings.companyId;
  const apiKey = settings.apiKey;

  if (!companyId) {
    throw new Error('会社IDが設定されていません。拡張の設定を確認してください。');
  }

  // リクエストペイロード構築
  const payload = {
    company_id: companyId,
    scout_service: 'ビズリーチ',
    scout_id: extractScoutId(candidateData.profileUrl),
    response_type: 'interested',
    candidate: {
      name: candidateData.name,
      email: candidateData.email || null,
      phone: candidateData.phone || null,
      profile_url: candidateData.profileUrl,
      profile: candidateData.profile || {},
    },
  };

  // ヘッダー構築
  const headers = {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  };

  if (apiKey) {
    headers['X-API-Key'] = apiKey;
  }

  // API送信
  const response = await fetch(`${atsUrl}/api/intake/scout`, {
    method: 'POST',
    headers,
    body: JSON.stringify(payload),
  });

  const responseData = await response.json();

  if (!response.ok) {
    const errorMsg =
      responseData.message ||
      responseData.error ||
      `HTTPエラー: ${response.status}`;
    throw new Error(errorMsg);
  }

  return {
    success: true,
    data: responseData,
  };
}

/**
 * URLからスカウトIDを抽出（重複チェック用）
 */
function extractScoutId(url) {
  if (!url) return null;

  // URLパスの最後のセグメントをIDとして使用
  try {
    const urlObj = new URL(url);
    const pathParts = urlObj.pathname.split('/').filter(Boolean);
    return pathParts.length > 0
      ? `bizreach_${pathParts[pathParts.length - 1]}`
      : null;
  } catch {
    return null;
  }
}
