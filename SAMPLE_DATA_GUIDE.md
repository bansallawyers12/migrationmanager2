# 📊 Sample Appointments Testing Guide

## ✅ Successfully Created Test Data!

Your booking appointments system now has **10 sample appointments** with realistic test data to help you test all features.

---

## 📋 What Was Created

### Test Clients (10 clients)
- John Smith - JOHN2503857
- Sarah Johnson - SARA2503857
- David Lee - DAVI2503857
- Maria Garcia - MARI2503857
- Robert Chen - ROBE2503857
- Emma Wilson - EMMA2503857
- Michael Brown - MICH2503857
- Lisa Anderson - LISA2503857
- James Taylor - JAME2503857
- Patricia Martinez - PATR2503857

### Consultants (5 consultants)
- Arun Kumar (Paid Services) - Melbourne
- Shubham/Yadwinder (JRP) - Melbourne
- Education Team - Melbourne
- Tourist Visa Team - Melbourne
- Adelaide Office - Adelaide

### Sample Appointments (10 appointments)

#### 📊 By Status:
- **Pending:** 4 appointments
- **Confirmed:** 2 appointments
- **Completed:** 2 appointments
- **Cancelled:** 1 appointment
- **No Show:** 1 appointment

#### 📅 By Consultant Type:
- **Paid Services:** 3 appointments
- **JRP:** 2 appointments
- **Education:** 2 appointments
- **Tourist:** 2 appointments
- **Adelaide:** 1 appointment

---

## 🧪 Test Scenarios Included

### 1. **Pending Appointments (Future)**
Test the pending appointment workflow:
- John Smith - TR visa consultation (Paid, Melbourne) - 2 days from now
- Sarah Johnson - JRP inquiry (Free, Melbourne) - 3 days from now
- David Lee - Student visa (Free, Melbourne) - 1 day from now
- Emma Wilson - TR visa (Paid, Adelaide) - 4 days from now

**What to Test:**
- View pending appointments
- Confirm appointments
- Send reminder emails/SMS
- Assign/reassign consultants
- Add admin notes

### 2. **Confirmed Appointments (Tomorrow)**
Test confirmed appointment handling:
- Maria Garcia - Tourist visa (Free, Melbourne) - Tomorrow 9:30 AM
- Robert Chen - PR Complex case (Paid, Melbourne) - Tomorrow 3:00 PM

**What to Test:**
- Reminder SMS sending (24h before)
- Mark as in-progress when client arrives
- Complete appointments after meeting
- Update payment status

### 3. **Completed Appointments (Past)**
Test historical data viewing:
- Michael Brown - 482 visa consultation (Paid) - 2 days ago
- Lisa Anderson - Student visa extension (Free) - 5 days ago

**What to Test:**
- View completed appointment history
- Review admin notes
- Generate reports
- Check payment records

### 4. **Cancelled Appointment**
Test cancellation workflow:
- James Taylor - Skill assessment (Free, Melbourne) - Cancelled 6 hours ago

**What to Test:**
- View cancellation reason
- Reschedule options
- Cancellation notifications

### 5. **No Show Appointment**
Test no-show handling:
- Patricia Martinez - Tourist visa (Free, Melbourne) - Yesterday 1:00 PM

**What to Test:**
- Mark no-show appointments
- Add follow-up notes
- Contact client for rescheduling

---

## 🚀 Quick Test Commands

### View All Appointments
```bash
php artisan tinker
>>> App\Models\BookingAppointment::count()
# Should return: 10

>>> App\Models\BookingAppointment::with('consultant', 'client')->get()
```

### View By Status
```bash
php artisan tinker
>>> App\Models\BookingAppointment::where('status', 'pending')->count()
# Should return: 4

>>> App\Models\BookingAppointment::where('status', 'confirmed')->count()
# Should return: 2
```

### View Upcoming Appointments
```bash
php artisan tinker
>>> App\Models\BookingAppointment::where('appointment_datetime', '>=', now())->count()
# Should return: 6 (pending + confirmed appointments)
```

### View By Consultant
```bash
php artisan tinker
>>> $consultant = App\Models\AppointmentConsultant::where('calendar_type', 'paid')->first()
>>> $consultant->appointments()->count()
# Should return: 3
```

---

## 🔄 Reset Sample Data

If you need to start fresh or regenerate the test data:

```bash
# Method 1: Using the custom reset command
php artisan booking:reset-samples

# Method 2: Delete and recreate manually
# Clear appointments
php artisan tinker
>>> App\Models\BookingAppointment::truncate()

# Recreate sample data
php artisan db:seed --class=SampleBookingAppointmentsSeeder
```

### Keep Test Clients
If you want to keep the test clients but reset appointments:
```bash
php artisan booking:reset-samples --keep-clients
```

---

## 🎯 Testing Checklist

Use this checklist to test all features:

### Viewing & Filtering
- [ ] View all appointments list
- [ ] Filter by status (pending, confirmed, completed, etc.)
- [ ] Filter by consultant type
- [ ] Filter by date range
- [ ] Filter by location (Melbourne/Adelaide)
- [ ] Search by client name/email

### Appointment Details
- [ ] View full appointment details
- [ ] See client information
- [ ] View payment details (for paid appointments)
- [ ] Read enquiry details
- [ ] Check sync information

### Status Management
- [ ] Confirm pending appointments
- [ ] Mark appointments as in-progress
- [ ] Complete appointments
- [ ] Cancel appointments (with reason)
- [ ] Mark as no-show

### Consultant Management
- [ ] View consultant assignments
- [ ] Reassign consultants
- [ ] View appointments by calendar type

### Notes & Communication
- [ ] Add admin notes
- [ ] View note history
- [ ] Send confirmation emails
- [ ] Send reminder SMS
- [ ] Test email templates

### Calendar Views
- [ ] View Paid Services calendar
- [ ] View JRP calendar
- [ ] View Education calendar
- [ ] View Tourist calendar
- [ ] View Adelaide calendar

### Client Integration
- [ ] Click client link from appointment
- [ ] View client profile
- [ ] See all client appointments
- [ ] Check client contact information

---

## 📊 Sample Data Details

### Appointment #1 - John Smith (Pending, Paid)
- **Date:** 2 days from now, 10:00 AM
- **Service:** Temporary Residency (TR)
- **Consultant:** Arun Kumar (Paid Services)
- **Payment:** $150.00 (Paid via Stripe)
- **Status:** Pending confirmation

### Appointment #2 - Sarah Johnson (Pending, Free)
- **Date:** 3 days from now, 2:30 PM
- **Service:** Job Ready Program (JRP)
- **Consultant:** Shubham/Yadwinder (JRP)
- **Payment:** Free consultation
- **Status:** Pending confirmation

### Appointment #3 - David Lee (Pending, Free)
- **Date:** Tomorrow, 11:00 AM
- **Service:** Student Visa / Education
- **Consultant:** Education Team
- **Payment:** Free consultation
- **Status:** Pending confirmation

### Appointment #4 - Maria Garcia (Confirmed, Free)
- **Date:** Tomorrow, 9:30 AM
- **Service:** Tourist Visa
- **Consultant:** Tourist Visa Team
- **Payment:** Free consultation
- **Status:** Confirmed (2 hours ago)
- **Notes:** "Confirmed via phone. Client will bring passport copies."

### Appointment #5 - Robert Chen (Confirmed, Paid)
- **Date:** Tomorrow, 3:00 PM
- **Service:** PR - Complex Case
- **Consultant:** Arun Kumar (Paid Services)
- **Payment:** $225.00 (Paid via Stripe, $25 discount with WELCOME10 promo)
- **Status:** Confirmed (1 day ago)
- **Reminder:** Not sent yet (will be sent automatically)

### Appointment #6 - Emma Wilson (Pending, Paid)
- **Date:** 4 days from now, 10:30 AM
- **Location:** Adelaide Office
- **Service:** Temporary Residency (TR)
- **Consultant:** Adelaide Office
- **Payment:** $150.00 (Paid via Stripe)
- **Status:** Pending confirmation

### Appointment #7 - Michael Brown (Completed, Paid)
- **Date:** 2 days ago, 2:00 PM
- **Service:** Temporary Residency (482 visa)
- **Consultant:** Arun Kumar (Paid Services)
- **Payment:** $150.00 (Paid via Stripe)
- **Status:** Completed (2 days ago at 2:20 PM)
- **Notes:** "Meeting completed successfully. Client satisfied with consultation."

### Appointment #8 - Lisa Anderson (Completed, Free)
- **Date:** 5 days ago, 10:00 AM
- **Service:** Student Visa Extension
- **Consultant:** Education Team
- **Payment:** Free consultation
- **Status:** Completed (5 days ago)
- **Reminder:** Sent (6 days ago at 9:00 AM)

### Appointment #9 - James Taylor (Cancelled, Free)
- **Date:** Was scheduled 5 days from now, 4:00 PM
- **Service:** Skill Assessment
- **Consultant:** Shubham/Yadwinder (JRP)
- **Payment:** Free consultation
- **Status:** Cancelled (6 hours ago)
- **Reason:** "Client requested to reschedule due to work commitment."

### Appointment #10 - Patricia Martinez (No Show, Free)
- **Date:** Yesterday, 1:00 PM
- **Service:** Tourist Visa
- **Consultant:** Tourist Visa Team
- **Payment:** Free consultation
- **Status:** No Show
- **Reminder:** Sent (2 days ago at 9:00 AM)
- **Notes:** "Client did not show up. Attempted to call - no answer."

---

## 💡 Tips for Testing

### 1. Test the Full Workflow
Follow a realistic appointment lifecycle:
1. View pending appointment (David Lee - Tomorrow)
2. Confirm it
3. Add admin notes
4. Test reminder sending
5. Mark as in-progress when "client arrives"
6. Complete the appointment
7. Add completion notes

### 2. Test Different Scenarios
- **Paid vs Free:** Compare John Smith (paid) vs Sarah Johnson (free)
- **Different Locations:** Compare Melbourne vs Adelaide (Emma Wilson)
- **Problem Cases:** Test cancelled (James Taylor) and no-show (Patricia Martinez)

### 3. Test Calendar Views
Each calendar type has appointments:
- **Paid Calendar:** John Smith, Robert Chen, Michael Brown
- **JRP Calendar:** Sarah Johnson, James Taylor
- **Education Calendar:** David Lee, Lisa Anderson
- **Tourist Calendar:** Maria Garcia, Patricia Martinez
- **Adelaide Calendar:** Emma Wilson

### 4. Test Filters & Search
Try these filter combinations:
- Status = "pending" + Location = "melbourne" (should show 3)
- Status = "confirmed" (should show 2)
- Paid appointments only (should show 5)
- Appointments tomorrow (should show 3)

---

## 🔍 Verify Installation

Run these quick checks to ensure everything is working:

```bash
# 1. Check appointments were created
php artisan tinker
>>> App\Models\BookingAppointment::count()
# Expected: 10

# 2. Check clients were created
>>> App\Models\Admin::where('role', 7)->where('source', 'Bansal Website (Test Data)')->count()
# Expected: 10

# 3. Check consultants exist
>>> App\Models\AppointmentConsultant::count()
# Expected: 5

# 4. Check relationships work
>>> $appointment = App\Models\BookingAppointment::first()
>>> $appointment->client->first_name
# Should return client name

>>> $appointment->consultant->name
# Should return consultant name
```

---

## 🎉 Next Steps

Now that you have sample data, you can:

1. **Test the UI:**
   - Navigate to the booking appointments section
   - View the appointment list
   - Click on appointments to see details

2. **Test Commands:**
   ```bash
   # Test reminder sending (won't actually send for sample data)
   php artisan booking:send-reminders
   
   # Test manual sync (will attempt API connection)
   php artisan booking:test-api
   ```

3. **Build Features:**
   - Use this data to test new features
   - Verify filtering and searching
   - Test email/SMS templates

4. **Performance Testing:**
   - All queries should be fast with 10 records
   - Can add more sample data if needed

---

## ❓ Troubleshooting

### Issue: No appointments showing
**Solution:**
```bash
php artisan db:seed --class=SampleBookingAppointmentsSeeder
```

### Issue: Consultants missing
**Solution:**
```bash
php artisan db:seed --class=AppointmentConsultantSeeder
```

### Issue: Need to start fresh
**Solution:**
```bash
php artisan booking:reset-samples
```

### Issue: Autoload errors
**Solution:**
```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

---

## 📝 Notes

- All sample emails use `@example.com` domain (not real)
- All phone numbers are Australian format (+61)
- Payment amounts are in AUD
- Appointment times are set relative to current date/time
- Admin notes include realistic scenarios
- All data is clearly marked as test data (source: "Bansal Website (Test Data)")

---

**Happy Testing! 🚀**

For questions or issues, check the main implementation plan:
- `APPOINTMENT_SYNC_IMPLEMENTATION_PLAN_COMPLETE.md`
- `APPOINTMENT_SYNC_MISSING_COMPONENTS.md`

