@loadOnce('bpFieldInitSwitchScript')
<script>
    function bpFieldInitSwitch($element) {
        let element = $element[0];
        let hiddenElement = element.previousElementSibling;
        let id = `switch_${hiddenElement.name}_${Math.random() * 1e18}`;

        // set unique IDs so that labels are correlated with inputs
        element.setAttribute('id', id);
        element.parentElement.nextElementSibling.setAttribute('for', id);

        // set the default checked/unchecked state
        // if the field has been loaded with javascript
        hiddenElement.value !== '0'
            ? element.setAttribute('checked', true)
            : element.removeAttribute('checked');

        // JS Field API
        $(hiddenElement).on('CrudField:disable', () => element.setAttribute('disabled', true));
        $(hiddenElement).on('CrudField:enable', () => element.removeAttribute('disabled'));

        // when the checkbox is clicked
        // set the correct value on the hidden input
        $element.on('change', () => {
            hiddenElement.value = element.checked ? 1 : 0;
            hiddenElement.dispatchEvent(new Event('change'));
        });
    }
</script>
@endLoadOnce

@loadOnce('bpFieldInitSwitchStyle')
<style>
    .switch-input:checked+.switch-slider {
        background-color: var(--bg-color);
    }
</style>
@endLoadOnce
