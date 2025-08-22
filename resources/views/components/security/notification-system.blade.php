<!-- ========================================
           SISTEMA DE NOTIFICACIONES
           ======================================== -->

class NotificationSystem {
constructor() {
this.container = null;
this.notifications = new Map();
this.counter = 0;
this.init();
}

init() {
this.createContainer();
}

createContainer() {
this.container = document.createElement('div');
this.container.className = 'notification-container';
document.body.appendChild(this.container);
}

show(message, type = 'info', options = {}) {
const id = `notification-${++this.counter}`;
const notification = this.createNotification(id, message, type, options);

this.container.appendChild(notification);
this.notifications.set(id, notification);

// Mostrar notificación
setTimeout(() => {
notification.classList.add('show');
}, 100);

// Configurar auto-close
if (options.autoClose !== false) {
const duration = options.duration || 5000;
this.autoClose(id, duration);
}

// Configurar progreso
if (options.showProgress !== false) {
this.showProgress(id, options.duration || 5000);
}

return id;
}

createNotification(id, message, type, options) {
const notification = document.createElement('div');
notification.className = `notification ${type}`;
notification.id = id;

const title = this.getTypeTitle(type);
const icon = this.getTypeIcon(type);

notification.innerHTML = `
<div class="notification-header">
    <h6 class="notification-title">
        <i class="${icon} me-2"></i>${title}
    </h6>
    <button class="notification-close" onclick="notificationSystem.close('${id}')">
        <i class="fas fa-times"></i>
    </button>
</div>
<div class="notification-body">
    ${message}
</div>
<div class="notification-progress">
    <div class="notification-progress-bar" style="width: 100%"></div>
</div>
`;

return notification;
}

getTypeTitle(type) {
const titles = {
'success': 'Éxito',
'warning': 'Advertencia',
'error': 'Error',
'info': 'Información'
};
return titles[type] || 'Notificación';
}

getTypeIcon(type) {
const icons = {
'success': 'fas fa-check-circle',
'warning': 'fas fa-exclamation-triangle',
'error': 'fas fa-times-circle',
'info': 'fas fa-info-circle'
};
return icons[type] || 'fas fa-bell';
}

close(id) {
const notification = this.notifications.get(id);
if (!notification) return;

notification.classList.remove('show');
notification.classList.add('slide-out');

setTimeout(() => {
if (notification.parentNode) {
notification.parentNode.removeChild(notification);
}
this.notifications.delete(id);
}, 300);
}

closeAll() {
this.notifications.forEach((notification, id) => {
this.close(id);
});
}

autoClose(id, duration) {
setTimeout(() => {
this.close(id);
}, duration);
}

showProgress(id, duration) {
const notification = this.notifications.get(id);
if (!notification) return;

const progressBar = notification.querySelector('.notification-progress-bar');
if (!progressBar) return;

const startTime = Date.now();
const animate = () => {
const elapsed = Date.now() - startTime;
const progress = Math.max(0, 100 - (elapsed / duration) * 100);

progressBar.style.width = `${progress}%`;

if (progress > 0 && elapsed < duration) { requestAnimationFrame(animate); } }; requestAnimationFrame(animate); } //
    Métodos de conveniencia success(message, options={}) { return this.show(message, 'success' , options); }
    warning(message, options={}) { return this.show(message, 'warning' , options); } error(message, options={}) { return
    this.show(message, 'error' , options); } info(message, options={}) { return this.show(message, 'info' , options); }
    } // Inicializar sistema de notificaciones const notificationSystem=new NotificationSystem(); // Funciones globales
    para compatibilidad function showNotification(message, type='info' , options={}) { return
    notificationSystem.show(message, type, options); } function showSuccess(message, options={}) { return
    notificationSystem.success(message, options); } function showWarning(message, options={}) { return
    notificationSystem.warning(message, options); } function showError(message, options={}) { return
    notificationSystem.error(message, options); } function showInfo(message, options={}) { return
    notificationSystem.info(message, options); }
