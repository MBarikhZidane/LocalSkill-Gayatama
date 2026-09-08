// Set the public Google Web client ID only after the backend callback is ready.
const GOOGLE_CLIENT_ID = '';
const googleButton = document.getElementById('google-signin');
const googleStatus = document.getElementById('google-status');
if (googleButton && GOOGLE_CLIENT_ID && /^https?:$/.test(location.protocol)) {
  googleStatus.textContent = 'Loading Google sign-in…';
  const script = document.createElement('script');
  script.src = 'https://accounts.google.com/gsi/client';
  script.async = true;
  script.onerror = () => { googleStatus.textContent = 'Google sign-in could not load. Check your connection and reload this page.'; };
  script.onload = () => {
    try {
      google.accounts.id.initialize({ client_id: GOOGLE_CLIENT_ID, ux_mode: 'redirect', login_uri: new URL('/auth/google/callback', location.origin).href });
      googleButton.replaceChildren();
      google.accounts.id.renderButton(googleButton, { type: 'standard', theme: 'outline', size: 'large', text: 'continue_with', shape: 'rectangular' });
      googleStatus.textContent = 'Use your Google account to sign in or create a LOCALSKILL account.';
    } catch {
      googleStatus.textContent = 'Google sign-in is unavailable. Please try again later.';
    }
  };
  document.head.append(script);
}
