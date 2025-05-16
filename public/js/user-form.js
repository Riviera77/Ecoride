/* 
Cache le bloc “Mes véhicules” au chargement 
L’afficher dès qu’un rôle = “chauffeur” est coché 
Le masquer si on le décoche 
*/

document.addEventListener('DOMContentLoaded', function () {
    const carFields = document.getElementById('car-fields');
    const driverPrefs = document.getElementById('driver-preferences');
    const roleCheckboxes = document.querySelectorAll('input[name="user1[roleType][]"]');

    function updateCarFieldsVisibility() {
        let isDriver = false;
        roleCheckboxes.forEach(cb => {
            if (cb.checked && cb.value === 'chauffeur') {
                isDriver = true;
            }
        });

        if (carFields) {
            carFields.style.display = isDriver ? 'block' : 'none';
        }

        if (driverPrefs) {
            driverPrefs.style.display = isDriver ? 'block' : 'none';
        }
    }

    updateCarFieldsVisibility();

    roleCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateCarFieldsVisibility);
    });
});