<!DOCTYPE html>
<html lang="fr" dir="rtl">
<head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Système de réservation de chambres</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap RTL -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-rtl@0.4.0/dist/css/bootstrap-rtl.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- FullCalendar -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css' rel='stylesheet' />
    
    <style>
        :root {
            --fc-event-stay: #28a745;
            --fc-event-conference: #dc3545;
            --fc-event-meeting: #ffc107;
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --light-color: #f8f9fc;
            --dark-color: #5a5c69;
        }
        
        body {
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f8f9fc;
        }
        
        .fc-event-stay { 
            background-color: var(--fc-event-stay) !important; 
            border-color: var(--fc-event-stay) !important; 
        }
        .fc-event-conference { 
            background-color: var(--fc-event-conference) !important; 
            border-color: var(--fc-event-conference) !important; 
        }
        .fc-event-meeting { 
            background-color: var(--fc-event-meeting) !important; 
            border-color: var(--fc-event-meeting) !important; 
            color: #000 !important; 
        }
        
        .calendar-container { 
            border: 1px solid #e3e6f0; 
            border-radius: 0.35rem; 
            background: white;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        .calendar-header { 
            background-color: var(--light-color); 
            padding: 1rem; 
            border-bottom: 1px solid #e3e6f0;
            border-radius: 0.35rem 0.35rem 0 0;
        }
        
        .room-item.active, 
        .room-type-card.active { 
            background-color: var(--primary-color) !important; 
            color: white !important; 
        }
        
        .fc-event { 
            cursor: pointer; 
            font-weight: 500;
            border-radius: 0.25rem;
        }
        
        .fc-event:hover { 
            opacity: 0.9; 
            transform: scale(1.01);
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        .fc-toolbar-title {
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .fc-col-header-cell-cushion {
            font-weight: 500;
            color: var(--dark-color);
        }
        
        .badge {
            font-weight: 500;
            padding: 0.5em 0.75em;
        }
        
        /* Sidebar styling */
        .sidebar {
            background: white;
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        .sidebar .card {
            border: none;
            box-shadow: none;
        }
        
        .sidebar .card-header {
            font-weight: 600;
            border-bottom: 1px solid #e3e6f0;
            background-color: var(--light-color);
            color: var(--dark-color);
        }
        
        .sidebar .list-group-item {
            border-left: none;
            border-right: none;
            padding: 0.75rem 1.25rem;
            border-color: #e3e6f0;
        }
        
        .sidebar .list-group-item:hover {
            background-color: var(--light-color);
        }
        
        /* Icon styling */
        .icon-primary {
            color: var(--primary-color);
        }
        
        .icon-success {
            color: var(--success-color);
        }
        
        .icon-info {
            color: var(--info-color);
        }
        
        .icon-warning {
            color: var(--warning-color);
        }
        
        .icon-danger {
            color: var(--danger-color);
        }
        
        /* Modal styling */
        .modal-header {
            border-bottom: 1px solid #e3e6f0;
            background-color: var(--light-color);
        }
        
        /* Button styling */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: #2e59d9;
            border-color: #2653d4;
        }
        
        /* Toast styling */
        .toast {
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        /* Responsive adjustments */
        @media (max-width: 992px) {
            .sidebar {
                margin-bottom: 1.5rem;
            }
        }
        
        /* Date display in calendar header */
        .fc-header-toolbar {
            padding: 0.5rem 1rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="text-center mb-4">
                    <i class="fas fa-calendar-alt icon-primary me-2"></i>Système de réservation de chambres
                </h1>
            </div>
        </div>

        <div class="row">
            <!-- Main Calendar -->
            <div class="col-lg-9 col-md-8 mb-4 order-md-1 order-2">
                <div class="calendar-container">
                    <div class="calendar-header d-flex justify-content-between align-items-center">
                        <h3 class="m-0">
                            <i class="far fa-calendar-alt icon-primary me-2"></i>Calendrier
                        </h3>
                        <div id="currentRoomSelection" class="badge bg-light text-dark">
                            <i class="fas fa-door-closed me-1"></i>Aucune chambre sélectionnée
                        </div>
                    </div>
                    <div id="calendar" class="p-3"></div>
                </div>
            </div>

            <!-- Sidebar - Right Side -->
            <div class="col-lg-3 col-md-4 mb-4 order-md-2 order-1">
                <div class="sidebar">
                    <!-- Room Types -->
                    <div class="card shadow-sm mb-3">
                        <div class="card-header">
                            <i class="fas fa-list icon-primary me-2"></i>Types de chambres
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush" id="roomTypeList">
                                @foreach($typeRooms as $typeRoom)
                                <a href="#" class="list-group-item list-group-item-action room-type-card" data-type-id="{{ $typeRoom->id }}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span><i class="fas fa-hotel me-2 text-muted"></i>{{ $typeRoom->name }}</span>
                                        <span class="badge bg-secondary rounded-pill">{{ $typeRoom->rooms->count() }}</span>
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Room List -->
                    <div class="card shadow-sm mb-3">
                        <div class="card-header">
                            <i class="fas fa-door-open icon-primary me-2"></i>Chambres disponibles
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush" id="roomList">
                                <div class="text-center py-3 text-muted">
                                    <i class="fas fa-info-circle me-2"></i>Veuillez d'abord sélectionner un type de chambre
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Activity Type Selector -->
                    <div class="card shadow-sm mb-3">
                        <div class="card-header">
                            <i class="fas fa-tags icon-primary me-2"></i>Type d'activité
                        </div>
                        <div class="card-body">
                            <select class="form-select" id="activityTypeSelect">
                                <option value="stay"><i class="fas fa-bed me-2"></i>Séjour</option>
                                <option value="conference"><i class="fas fa-users me-2"></i>Conférence</option>
                                <option value="meeting"><i class="fas fa-handshake me-2"></i>Réunion</option>
                            </select>
                        </div>
                    </div>

                    <!-- Legend -->
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <i class="fas fa-info-circle icon-primary me-2"></i>Légende
                        </div>
                        <div class="card-body">
                            <div class="mb-2 d-flex align-items-center">
                                <i class="fas fa-square me-2" style="color: var(--fc-event-stay);"></i>
                                <span>Séjour</span>
                            </div>
                            <div class="mb-2 d-flex align-items-center">
                                <i class="fas fa-square me-2" style="color: var(--fc-event-conference);"></i>
                                <span>Conférence</span>
                            </div>
                            <div class="mb-2 d-flex align-items-center">
                                <i class="fas fa-square me-2" style="color: var(--fc-event-meeting);"></i>
                                <span>Réunion</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reservation Modal -->
    <div class="modal fade" id="reservationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-calendar-plus me-2"></i>Nouvelle réservation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="reservationForm">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="fas fa-user me-2"></i>Nom du client</label>
                                <input type="text" class="form-control" id="clientName" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="fas fa-phone me-2"></i>Téléphone</label>
                                <input type="tel" class="form-control" id="clientPhone">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="fas fa-door-open me-2"></i>Chambre</label>
                                <input type="text" class="form-control" id="selectedRoom" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="fas fa-tag me-2"></i>Type d'activité</label>
                                <select class="form-select" id="modalActivityType">
                                    <option value="stay"><i class="fas fa-bed me-2"></i>Séjour</option>
                                    <option value="conference"><i class="fas fa-users me-2"></i>Conférence</option>
                                    <option value="meeting"><i class="fas fa-handshake me-2"></i>Réunion</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="fas fa-calendar-day me-2"></i>Date de début</label>
                                <div class="input-group">
                                    <input type="date" class="form-control" id="startDate" required>
                                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="fas fa-calendar-day me-2"></i>Date de fin</label>
                                <div class="input-group">
                                    <input type="date" class="form-control" id="endDate" required>
                                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-sticky-note me-2"></i>Notes</label>
                            <textarea class="form-control" id="reservationNotes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Annuler
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Event Details Modal -->
    <div class="modal fade" id="eventDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i>Détails de la réservation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <strong><i class="fas fa-user me-2"></i>Client:</strong> <span id="eventClientName"></span>
                    </div>
                    <div class="mb-3">
                        <strong><i class="fas fa-phone me-2"></i>Téléphone:</strong> <span id="eventClientPhone"></span>
                    </div>
                    <div class="mb-3">
                        <strong><i class="fas fa-door-open me-2"></i>Chambre:</strong> <span id="eventRoom"></span>
                    </div>
                    <div class="mb-3">
                        <strong><i class="fas fa-tag me-2"></i>Type d'activité:</strong> <span id="eventActivityType"></span>
                    </div>
                    <div class="mb-3">
                        <strong><i class="fas fa-calendar-day me-2"></i>Dates:</strong> 
                        <span id="eventStartDate"></span> au <span id="eventEndDate"></span>
                    </div>
                    <div class="mb-3">
                        <strong><i class="fas fa-sticky-note me-2"></i>Notes:</strong> 
                        <div id="eventNotes" class="mt-2 p-2 bg-light rounded"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" id="extendEventBtn">
                        <i class="fas fa-expand-arrows-alt me-2"></i>Prolonger
                    </button>
                    <button type="button" class="btn btn-danger" id="deleteEventBtn">
                        <i class="fas fa-trash me-2"></i>Supprimer
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Extend Event Modal -->
    <div class="modal fade" id="extendEventModal" tabindex="-1" aria-hidden="true" aria-modal="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-expand-arrows-alt me-2"></i>Prolonger la réservation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="extendEventForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-calendar-day me-2"></i>Nouvelle date de fin</label>
                            <div class="input-group">
                                <input type="date" class="form-control" id="newEndDate" required>
                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                            </div>
                        </div>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            La réservation sera prolongée jusqu'à la nouvelle date.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Annuler
                        </button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save me-2"></i>Prolonger
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Toast Notifications -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto" id="toastTitle">Notification</strong>
                <small id="toastTime"></small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toastMessage">
                <i class="fas fa-info-circle me-2"></i>
                <span id="toastContent"></span>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/luxon@3.0.1/build/global/luxon.min.js"></script>

    <script>
document.addEventListener('DOMContentLoaded', function () {
    const reservationModal = new bootstrap.Modal(document.getElementById('reservationModal'));
    const eventDetailsModal = new bootstrap.Modal(document.getElementById('eventDetailsModal'));
    const extendEventModal = new bootstrap.Modal(document.getElementById('extendEventModal'));
    const toast = new bootstrap.Toast(document.getElementById('liveToast'));
    
    let calendar;
    let selectedRoom = null;
    let currentEvent = null;
    
    // CSRF Token
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;
    
    function afficherToast(titre, message, type = 'info') {
        const toastEl = document.getElementById('liveToast');
        const toastHeader = toastEl.querySelector('.toast-header');
        const toastContent = toastEl.querySelector('#toastContent');
        const toastTitle = document.getElementById('toastTitle');
        const toastTime = document.getElementById('toastTime');
        
        // Set current time
        const now = new Date();
        toastTime.textContent = now.toLocaleTimeString('fr-FR', {hour: '2-digit', minute:'2-digit'});
        
        // Set content
        toastTitle.textContent = titre;
        toastContent.textContent = message;
        
        // Update icon and color
        const icon = toastEl.querySelector('.toast-body i');
        icon.className = 'fas me-2';
        
        switch (type) {
            case 'success': 
                toastHeader.className = 'toast-header bg-success text-white';
                icon.className += ' fa-check-circle';
                break;
            case 'danger': 
                toastHeader.className = 'toast-header bg-danger text-white';
                icon.className += ' fa-exclamation-circle';
                break;
            case 'warning': 
                toastHeader.className = 'toast-header bg-warning text-dark';
                icon.className += ' fa-exclamation-triangle';
                break;
            default: 
                toastHeader.className = 'toast-header bg-primary text-white';
                icon.className += ' fa-info-circle';
        }
        
        toast.show();
    }

    function formatDate(dateStr) {
        const dt = luxon.DateTime.fromISO(dateStr);
        return dt.setLocale('fr').toLocaleString(luxon.DateTime.DATE_FULL);
    }

    function getActivityTypeLabel(type) {
        const labels = {
            'stay': '<i class="fas fa-bed me-2"></i>Séjour',
            'conference': '<i class="fas fa-users me-2"></i>Conférence',
            'meeting': '<i class="fas fa-handshake me-2"></i>Réunion'
        };
        return labels[type] || type;
    }

    function getRoomName(roomId) {
        const roomItems = document.querySelectorAll('.room-item');
        for (let item of roomItems) {
            if (item.dataset.roomId == roomId) {
                const typeCard = document.querySelector('.room-type-card.active');
                if (typeCard) {
                    const typeName = typeCard.querySelector('span:first-child').textContent;
                    return `${typeName} - ${item.textContent}`;
                }
                return item.textContent;
            }
        }
        return 'Chambre inconnue';
    }

    function checkAvailability(roomId, startDate, endDate, activityType) {
        return axios.post('/events/check-availability', {
            room_id: roomId,
            start_date: startDate,
            end_date: endDate,
            activity_type: activityType
        })
        .then(response => response.data.available)
        .catch(error => {
            console.error('Error checking availability:', error);
            return false;
        });
    }

    function initialiserCalendrier() {
        const calendarEl = document.getElementById('calendar');
        calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'fr',
            direction: 'rtl',
            initialView: 'dayGridMonth',
            selectable: true,
            editable: true,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek'
            },
            buttonText: {
                today: 'Aujourd\'hui',
                month: 'Mois',
                week: 'Semaine'
            },
            events: '/events',
            eventDidMount: function(info) {
                info.el.classList.add(`fc-event-${info.event.extendedProps.activity_type || 'stay'}`);
                
                // Add custom tooltip
                const tooltipContent = `
                    <strong>${info.event.title}</strong><br>
                    ${getRoomName(info.event.extendedProps.room_id)}<br>
                    ${formatDate(info.event.startStr)} - ${formatDate(info.event.endStr)}
                `;
                
                info.el.setAttribute('data-bs-toggle', 'tooltip');
                info.el.setAttribute('data-bs-html', 'true');
                info.el.setAttribute('title', tooltipContent);
                
                // Initialize tooltip
                new bootstrap.Tooltip(info.el);
            },
            select: async function(info) {
                if (!selectedRoom) {
                    afficherToast('Attention', 'Veuillez d\'abord sélectionner une chambre.', 'warning');
                    calendar.unselect();
                    return;
                }

                if (selectedRoom.id == 1) {
                    afficherToast('Refusé', 'La chambre 1 ne peut jamais être réservée.', 'danger');
                    calendar.unselect();
                    return;
                }

                const typeActivite = document.getElementById('activityTypeSelect').value;
                let startDate = info.startStr;
                let endDate = info.endStr;

                // Fix end date for all-day events
                if (info.allDay && info.end) {
                    const endDateObj = new Date(info.end);
                    endDateObj.setDate(endDateObj.getDate() - 1);
                    endDate = endDateObj.toISOString().split('T')[0];
                }

                const isAvailable = await checkAvailability(selectedRoom.id, startDate, endDate, typeActivite);
                if (!isAvailable) {
                    afficherToast('Occupée', 'Cette chambre est déjà réservée à cette période.', 'warning');
                    calendar.unselect();
                    return;
                }

                ouvrirModalReservation(selectedRoom, startDate, endDate, typeActivite);
                calendar.unselect();
            },
            eventClick: function(info) {
                const event = info.event;
                currentEvent = {
                    id: event.id,
                    title: event.title,
                    start: event.startStr,
                    end: event.endStr,
                    room_id: event.extendedProps.room_id,
                    activity_type: event.extendedProps.activity_type,
                    client_name: event.extendedProps.client_name,
                    client_phone: event.extendedProps.client_phone || 'Non spécifié',
                    notes: event.extendedProps.notes || 'Aucune note'
                };
                
                document.getElementById('eventClientName').textContent = event.extendedProps.client_name;
                document.getElementById('eventClientPhone').textContent = event.extendedProps.client_phone || 'Non spécifié';
                document.getElementById('eventRoom').textContent = getRoomName(event.extendedProps.room_id);
                document.getElementById('eventActivityType').innerHTML = getActivityTypeLabel(event.extendedProps.activity_type);
                document.getElementById('eventStartDate').textContent = formatDate(event.startStr);
                document.getElementById('eventEndDate').textContent = formatDate(event.endStr);
                document.getElementById('eventNotes').textContent = event.extendedProps.notes || 'Aucune note';
                
                eventDetailsModal.show();
            },
            eventDrop: function(info) {
                const event = info.event;
                const startDate = event.start.toISOString().split('T')[0];
                let endDate = event.end ? event.end.toISOString().split('T')[0] : startDate;
                
                // Adjust end date for all-day events
                if (event.allDay && event.end) {
                    const endDateObj = new Date(event.end);
                    endDateObj.setDate(endDateObj.getDate() - 1);
                    endDate = endDateObj.toISOString().split('T')[0];
                }

                axios.put(`/events/${event.id}`, {
                    start_date: startDate,
                    end_date: endDate
                })
                .then(response => {
                    afficherToast('Succès', 'Réservation déplacée avec succès !', 'success');
                })
                .catch(error => {
                    console.error('Error updating reservation:', error);
                    info.revert();
                    afficherToast('Erreur', 'Conflit de réservation pour cette période.', 'danger');
                });
            },
            eventResize: function(info) {
                const event = info.event;
                const startDate = event.start.toISOString().split('T')[0];
                let endDate = event.end ? event.end.toISOString().split('T')[0] : startDate;
                
                // Adjust end date for all-day events
                if (event.allDay && event.end) {
                    const endDateObj = new Date(event.end);
                    endDateObj.setDate(endDateObj.getDate() - 1);
                    endDate = endDateObj.toISOString().split('T')[0];
                }

                axios.put(`/events/${event.id}`, {
                    start_date: startDate,
                    end_date: endDate
                })
                .then(response => {
                    afficherToast('Succès', 'Réservation mise à jour avec succès !', 'success');
                })
                .catch(error => {
                    console.error('Error updating reservation:', error);
                    info.revert();
                    afficherToast('Erreur', 'Conflit de réservation pour cette période.', 'danger');
                });
            }
        });

        calendar.render();
    }

    function initialiserTypeChambres() {
        const roomTypeList = document.getElementById('roomTypeList');
        const roomList = document.getElementById('roomList');

        roomTypeList.addEventListener('click', function (e) {
            e.preventDefault();
            const carte = e.target.closest('.room-type-card');
            if (!carte) return;

            document.querySelectorAll('.room-type-card').forEach(card => card.classList.remove('active'));
            carte.classList.add('active');

            const typeId = carte.dataset.typeId;
            roomList.innerHTML = '';

            axios.get(`/type-rooms/${typeId}/rooms`)
            .then(response => {
                const rooms = response.data;
                if (rooms.length === 0) {
                    roomList.innerHTML = '<div class="text-center py-3 text-muted"><i class="fas fa-door-closed me-2"></i>Aucune chambre disponible</div>';
                    return;
                }

                rooms.forEach(room => {
                    const item = document.createElement('a');
                    item.href = '#';
                    item.className = 'list-group-item list-group-item-action room-item';
                    item.dataset.roomId = room.id;
                    
                    const icon = document.createElement('i');
                    icon.className = 'fas fa-door-open me-2';
                    
                    item.appendChild(icon);
                    item.appendChild(document.createTextNode(room.name));

                    item.addEventListener('click', function (e) {
                        e.preventDefault();
                        document.querySelectorAll('.room-item').forEach(i => i.classList.remove('active'));
                        this.classList.add('active');

                        selectedRoom = {
                            id: room.id,
                            name: room.name,
                            type: carte.querySelector('span:first-child').textContent
                        };

                        const badge = document.getElementById('currentRoomSelection');
                        badge.innerHTML = `<i class="fas fa-door-open me-1"></i>${selectedRoom.type} - ${selectedRoom.name}`;
                        badge.className = 'badge bg-success';

                        calendar.refetchEvents();
                    });

                    roomList.appendChild(item);
                });
            })
            .catch(error => {
                console.error('Error fetching rooms:', error);
                roomList.innerHTML = '<div class="text-center py-3 text-muted"><i class="fas fa-exclamation-triangle me-2"></i>Erreur de chargement</div>';
            });
        });
    }

    function ouvrirModalReservation(room, debut, fin, activite) {
        document.getElementById('selectedRoom').value = `${room.type} - ${room.name}`;
        document.getElementById('startDate').value = debut;
        document.getElementById('endDate').value = fin;
        document.getElementById('modalActivityType').value = activite;
        document.getElementById('clientName').value = '';
        document.getElementById('clientPhone').value = '';
        document.getElementById('reservationNotes').value = '';
        reservationModal.show();
    }

    // Event Listeners
    document.getElementById('reservationForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const clientName = document.getElementById('clientName').value.trim();
        const clientPhone = document.getElementById('clientPhone').value.trim();
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const activityType = document.getElementById('modalActivityType').value;
        const notes = document.getElementById('reservationNotes').value.trim();

        if (!clientName || !startDate || !endDate) {
            afficherToast('Attention', 'Tous les champs obligatoires doivent être remplis.', 'warning');
            return;
        }

        if (startDate > endDate) {
            afficherToast('Erreur', 'La date de fin doit être après la date de début.', 'danger');
            return;
        }

        axios.post('/events', {
            room_id: selectedRoom.id,
            client_name: clientName,
            client_phone: clientPhone,
            start_date: startDate,
            end_date: endDate,
            activity_type: activityType,
            notes: notes
        })
        .then(response => {
            afficherToast('Succès', 'Réservation ajoutée avec succès.', 'success');
            reservationModal.hide();
            calendar.refetchEvents();
        })
        .catch(error => {
            console.error('Error creating reservation:', error);
            const message = error.response?.data?.message || 'Erreur lors de la création de la réservation';
            afficherToast('Erreur', message, 'danger');
        });
    });

    // Delete event handler
    document.getElementById('deleteEventBtn').addEventListener('click', function() {
        if (!currentEvent) return;

        if (confirm('Êtes-vous sûr de vouloir supprimer cette réservation ?')) {
            axios.delete(`/events/${currentEvent.id}`)
            .then(response => {
                eventDetailsModal.hide();
                calendar.refetchEvents();
                afficherToast('Succès', 'Réservation supprimée avec succès.', 'success');
            })
            .catch(error => {
                console.error('Error deleting reservation:', error);
                afficherToast('Erreur', 'Échec de la suppression de la réservation.', 'danger');
            });
        }
    });

    // Extend event
    document.getElementById('extendEventBtn').addEventListener('click', function() {
        if (!currentEvent) return;
        
        document.getElementById('newEndDate').value = currentEvent.end;
        document.getElementById('newEndDate').min = currentEvent.end;
        
        eventDetailsModal.hide();
        extendEventModal.show();
    });

    document.getElementById('extendEventForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!currentEvent) return;
        
        const newEndDate = document.getElementById('newEndDate').value;
        
        if (newEndDate <= currentEvent.end) {
            afficherToast('Erreur', 'La nouvelle date doit être après la date de fin actuelle.', 'danger');
            return;
        }

        axios.post(`/events/${currentEvent.id}/extend`, {
            new_end_date: newEndDate
        })
        .then(response => {
            extendEventModal.hide();
            calendar.refetchEvents();
            afficherToast('Succès', 'Réservation prolongée avec succès.', 'success');
        })
        .catch(error => {
            console.error('Error extending reservation:', error);
            const message = error.response?.data?.message || 'Erreur lors de la prolongation de la réservation';
            afficherToast('Erreur', message, 'danger');
        });
    });

    // Initialize
    initialiserCalendrier();
    initialiserTypeChambres();
});
    </script>
</body>
</html>