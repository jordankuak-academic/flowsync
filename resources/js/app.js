const routeName = document.querySelector('meta[name="route-name"]')?.content || '';

if (routeName === 'dashboard') {
  import('./modules/dashboard')
    .then(({ default: Dashboard }) => {
      Dashboard.init?.();
    })
    .catch((error) => {
      console.error('Failed to load dashboard module:', error);
    });
}
