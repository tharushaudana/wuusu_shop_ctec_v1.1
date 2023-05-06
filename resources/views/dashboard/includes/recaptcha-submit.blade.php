<script>
    function onClickSubmit(e, btn) {
        e.preventDefault();

        const form = btn.parentNode;

        const input = form.querySelector('input[name="g-recaptcha-response"]');

        const btnHtml = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin-pulse"></i>';

        grecaptcha.ready(function() {
            grecaptcha.execute("{{ config('services.recaptcha.site_key') }}", {action: 'submit'}).then(function(token) {
                input.value = token;
                
                form.submit();

                btn.innerHTML = btnHtml;
                btn.disabled = false;
            });
        });
    }
</script>