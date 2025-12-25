function checkFacebookLogin() {
    FB.getLoginStatus(function(response) {
      if (response.status === 'connected') {
        FB.api('/me', { fields: 'name,email' }, function(profile) {
          fetch('php/social_login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              provider: 'facebook',
              id: profile.id,
              name: profile.name,
              email: profile.email
            })
          }).then(res => res.json()).then(data => {
            alert('Đăng nhập Facebook thành công');
            // window.location.href = 'dashboard.html';
          });
        });
      }
    });
  }
  