
// Destination management functions
function checkDestinationPackages(destinationId) {
    fetch(`check-packages.php?destination_id=${destinationId}`)
        .then(response => response.json())
        .then(data => {
            const warningDiv = document.getElementById(`warning-${destinationId}`);
            const warningMessage = warningDiv.querySelector('.warning-message');
            
            if (data.package_count > 0) {
                warningMessage.textContent = `This destination has ${data.package_count} associated package(s). Are you sure you want to delete it?`;
                warningDiv.style.display = 'block';
            } else {
                if (confirm('Are you sure you want to delete this destination?')) {
                    deleteDestination(destinationId);
                }
            }
        });
}

function hideWarning(destinationId) {
    document.getElementById(`warning-${destinationId}`).style.display = 'none';
}

function deleteDestination(destinationId) {
    fetch(`delete-destination.php?id=${destinationId}&confirm=yes`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove the destination row from the table
                const row = document.querySelector(`tr[data-destination-id="${destinationId}"]`);
                if (row) {
                    row.remove();
                }
                showToast('Destination deleted successfully', 'success');
            } else {
                showToast(data.message || 'Error deleting destination', 'error');
            }
        });
}


document.addEventListener('DOMContentLoaded', function() {
    // Handle package status changes
    document.querySelectorAll('.package-status-select').forEach(select => {
        select.addEventListener('change', function() {
            const packageId = this.dataset.packageId;
            const status = this.value;
            const originalStatus = this.getAttribute('data-original-value');
            const selectElement = this;

            fetch('update_package_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `package_id=${packageId}&status=${status}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update all status selects for the same package
                    document.querySelectorAll(`.package-status-select[data-package-id="${packageId}"]`).forEach(otherSelect => {
                        otherSelect.value = status;
                        otherSelect.setAttribute('data-original-value', status);
                    });
                    // Show success toast or feedback
                    const toast = document.createElement('div');
                    toast.className = 'alert alert-success position-fixed bottom-0 end-0 m-3';
                    toast.innerHTML = 'Status updated successfully';
                    document.body.appendChild(toast);
                    setTimeout(() => toast.remove(), 3000);
                } else {
                    alert('Failed to update package status: ' + data.message);
                    selectElement.value = originalStatus;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the package status');
                selectElement.value = originalStatus;
            });
        });
        //Store initial value for rollback
        select.setAttribute('data-original-value', select.value);
    });


    // Enable Bootstrap tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize all modals
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        new bootstrap.Modal(modal);
    });

    // Newsletter subscription
    const newsletterForm = document.querySelector('.newsletter .input-group');
    if (newsletterForm) {
        const emailInput = newsletterForm.querySelector('input[type="email"]');
        const subscribeBtn = newsletterForm.querySelector('button');
        
        subscribeBtn.addEventListener('click', function() {
            const email = emailInput.value.trim();
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (emailPattern.test(email)) {
                alert('Subscribed!');
                emailInput.value = '';
            } else {
                alert('Please enter a valid email address');
            }
        });
    }

    // Cookie consent
    if (!localStorage.getItem('cookieConsent')) {
        const cookieConsent = document.getElementById('cookieConsent');
        if (cookieConsent) {
            cookieConsent.classList.add('show');
        }
    }

    document.getElementById('acceptCookies')?.addEventListener('click', () => {
        localStorage.setItem('cookieConsent', 'accepted');
        document.getElementById('cookieConsent').classList.remove('show');
    });

    // Package booking form functionality
    const bookingForm = document.getElementById('bookingForm');
    if (bookingForm) {
        const travelersInput = document.getElementById('numTravelers');
        const travelDateInput = document.getElementById('travelDate');
        const pricePerPersonEl = document.getElementById('pricePerPerson');
        const totalPriceEl = document.getElementById('totalPrice');
        const paymentMethodSelect = document.getElementById('paymentMethod');
        const gcashAmount = document.querySelector('.payment-instructions .payment-amount');
        const bankAmount = document.querySelector('.payment-instructions .payment-amount');

        // Set minimum date for travel date input
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        const tomorrowFormatted = tomorrow.toISOString().split('T')[0];
        travelDateInput.setAttribute('min', tomorrowFormatted);

        function calculateTotal() {
            if (travelersInput && pricePerPersonEl && totalPriceEl) {
                const pricePerPerson = parseFloat(pricePerPersonEl.dataset.price);
                const numTravelers = parseInt(travelersInput.value);
                const totalPrice = pricePerPerson * numTravelers;

                totalPriceEl.textContent = '₱' + totalPrice.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });

                // Update hidden total price input
                document.getElementById('totalPriceInput').value = totalPrice;
            }
        }

        // Calculate total on input change
        if (travelersInput) {
            travelersInput.addEventListener('input', calculateTotal);
            // Initial calculation
            calculateTotal();
        }
    }

    // Search destination functionality
    const searchForm = document.querySelector('form[action="destinations.php"]');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            const searchInput = this.querySelector('input[name="search"]');
            if (searchInput && searchInput.value.trim() === '') {
                e.preventDefault();
                alert('Please enter a destination to search.');
            }
        });
    }
});