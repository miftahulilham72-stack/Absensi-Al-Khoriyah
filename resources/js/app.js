import '../css/app.css';

document.addEventListener('DOMContentLoaded', function() {
    console.log('Al-Khoeriyah Attendance System loaded');

    // Auto close flash messages
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
});