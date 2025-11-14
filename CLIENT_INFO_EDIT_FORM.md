# Client Information Edit Form - Flutter Implementation Guide

## Overview
This document provides comprehensive information about the Client Information Edit Form that can be used to create the same form in the Flutter client portal app.

## API Endpoint

**Endpoint:** `PUT /api/profile`  
**Authentication:** Bearer Token (required)  
**Content-Type:** `application/json`

## Form Fields

### Personal Information

#### 1. First Name
- **Field Name:** `first_name`
- **Type:** Text Input
- **Required:** Yes
- **Validation:** 
  - String
  - Max length: 255 characters
- **Flutter Widget:** `TextFormField`
- **Example:** "John"

#### 2. Last Name
- **Field Name:** `last_name`
- **Type:** Text Input
- **Required:** No (optional)
- **Validation:** 
  - String
  - Max length: 255 characters
- **Flutter Widget:** `TextFormField`
- **Example:** "Doe"

#### 3. Date of Birth
- **Field Name:** `dob`
- **Type:** Date Picker
- **Required:** No (optional)
- **Validation:** 
  - Date format: `YYYY-MM-DD`
  - Must be before today's date
- **Flutter Widget:** `showDatePicker` or date picker package
- **Example:** "1990-01-15"
- **Note:** Format should be YYYY-MM-DD when sending to API

#### 4. Gender
- **Field Name:** `gender`
- **Type:** Radio Buttons / Dropdown
- **Required:** No (optional)
- **Validation:** 
  - Must be one of: `Male`, `Female`, `Other`
- **Flutter Widget:** `RadioListTile` or `DropdownButtonFormField`
- **Options:**
  - Male
  - Female
  - Other

#### 5. Marital Status
- **Field Name:** `marital_status`
- **Type:** Dropdown
- **Required:** No (optional)
- **Validation:** 
  - Must be one of: `Single`, `Married`, `Divorced`, `Widowed`, `Other`
- **Flutter Widget:** `DropdownButtonFormField`
- **Options:**
  - Single
  - Married
  - Divorced
  - Widowed
  - Other

### Contact Information

#### 6. Phone
- **Field Name:** `phone`
- **Type:** Text Input (Phone)
- **Required:** No (optional)
- **Validation:** 
  - String
  - Max length: 255 characters
- **Flutter Widget:** `TextFormField` with `TextInputType.phone`
- **Example:** "+1234567890" or "1234567890"
- **Note:** Can include country code

#### 7. Address
- **Field Name:** `address`
- **Type:** Text Input (Multi-line)
- **Required:** No (optional)
- **Validation:** 
  - String
  - Max length: 500 characters
- **Flutter Widget:** `TextFormField` with `maxLines: 2` or `TextField`
- **Example:** "123 Main Street, Apt 4B"

#### 8. City
- **Field Name:** `city`
- **Type:** Text Input
- **Required:** No (optional)
- **Validation:** 
  - String
  - Max length: 255 characters
- **Flutter Widget:** `TextFormField`
- **Example:** "Los Angeles"

#### 9. State
- **Field Name:** `state`
- **Type:** Text Input
- **Required:** No (optional)
- **Validation:** 
  - String
  - Max length: 255 characters
- **Flutter Widget:** `TextFormField`
- **Example:** "California" or "CA"

#### 10. Post Code / ZIP Code
- **Field Name:** `post_code`
- **Type:** Text Input
- **Required:** No (optional)
- **Validation:** 
  - String
  - Max length: 20 characters
- **Flutter Widget:** `TextFormField`
- **Example:** "90210" or "12345"

#### 11. Country
- **Field Name:** `country`
- **Type:** Text Input / Dropdown
- **Required:** No (optional)
- **Validation:** 
  - String
  - Max length: 255 characters
- **Flutter Widget:** `TextFormField` or `DropdownButtonFormField`
- **Example:** "United States" or "USA"

## API Request Format

### Request Headers
```
Authorization: Bearer {your_auth_token}
Content-Type: application/json
```

### Request Body (All fields are optional except validation rules)
```json
{
  "first_name": "John",
  "last_name": "Doe",
  "phone": "+1234567890",
  "address": "123 Main Street, Apt 4B",
  "city": "Los Angeles",
  "state": "CA",
  "post_code": "90210",
  "country": "United States",
  "dob": "1990-01-15",
  "gender": "Male",
  "marital_status": "Single"
}
```

### Success Response (200)
```json
{
  "success": true,
  "message": "Profile updated successfully",
  "data": {
    "id": 1,
    "client_id": "CL001",
    "first_name": "John",
    "last_name": "Doe",
    "email": "john.doe@example.com",
    "phone": "+1234567890",
    "address": "123 Main Street, Apt 4B",
    "city": "Los Angeles",
    "state": "CA",
    "zip": "90210",
    "country": "United States",
    "profile_img": null,
    "status": "active",
    "role": "client",
    "cp_status": "active",
    "cp_code_verify": 1,
    "email_verified_at": "2024-01-01T00:00:00.000000Z",
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-15T10:30:00.000000Z"
  }
}
```

### Error Response (422 - Validation Error)
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "first_name": ["The first name field is required."],
    "dob": ["The dob must be a date before today."],
    "gender": ["The selected gender is invalid."]
  }
}
```

### Error Response (404 - Not Found)
```json
{
  "success": false,
  "message": "Client not found"
}
```

### Error Response (500 - Server Error)
```json
{
  "success": false,
  "message": "Failed to update profile"
}
```

## Flutter Implementation Guide

### 1. Form Structure

Create a form with the following sections:

#### Section 1: Personal Information
- First Name (required)
- Last Name
- Date of Birth
- Gender
- Marital Status

#### Section 2: Contact Information
- Phone
- Address
- City
- State
- Post Code
- Country

### 2. Form State Management

```dart
class ClientInfoFormState {
  String? firstName;
  String? lastName;
  DateTime? dateOfBirth;
  String? gender;
  String? maritalStatus;
  String? phone;
  String? address;
  String? city;
  String? state;
  String? postCode;
  String? country;
}
```

### 3. Validation Rules

```dart
// First Name
if (firstName == null || firstName.isEmpty) {
  return 'First name is required';
}
if (firstName.length > 255) {
  return 'First name must be less than 255 characters';
}

// Last Name
if (lastName != null && lastName.length > 255) {
  return 'Last name must be less than 255 characters';
}

// Date of Birth
if (dateOfBirth != null && dateOfBirth.isAfter(DateTime.now())) {
  return 'Date of birth must be before today';
}

// Gender
if (gender != null && !['Male', 'Female', 'Other'].contains(gender)) {
  return 'Invalid gender selection';
}

// Marital Status
if (maritalStatus != null && 
    !['Single', 'Married', 'Divorced', 'Widowed', 'Other'].contains(maritalStatus)) {
  return 'Invalid marital status';
}

// Phone
if (phone != null && phone.length > 255) {
  return 'Phone must be less than 255 characters';
}

// Address
if (address != null && address.length > 500) {
  return 'Address must be less than 500 characters';
}

// City, State, Country
if (city != null && city.length > 255) {
  return 'City must be less than 255 characters';
}
// Similar for state and country

// Post Code
if (postCode != null && postCode.length > 20) {
  return 'Post code must be less than 20 characters';
}
```

### 4. API Call Example

```dart
Future<void> updateProfile() async {
  try {
    final response = await http.put(
      Uri.parse('$baseUrl/api/profile'),
      headers: {
        'Authorization': 'Bearer $authToken',
        'Content-Type': 'application/json',
      },
      body: jsonEncode({
        if (firstName != null) 'first_name': firstName,
        if (lastName != null) 'last_name': lastName,
        if (phone != null) 'phone': phone,
        if (address != null) 'address': address,
        if (city != null) 'city': city,
        if (state != null) 'state': state,
        if (postCode != null) 'post_code': postCode,
        if (country != null) 'country': country,
        if (dateOfBirth != null) 'dob': DateFormat('yyyy-MM-dd').format(dateOfBirth!),
        if (gender != null) 'gender': gender,
        if (maritalStatus != null) 'marital_status': maritalStatus,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      // Handle success
    } else if (response.statusCode == 422) {
      final errors = jsonDecode(response.body)['errors'];
      // Handle validation errors
    } else {
      // Handle other errors
    }
  } catch (e) {
    // Handle exception
  }
}
```

### 5. Form Widget Structure

```dart
Form(
  key: _formKey,
  child: Column(
    children: [
      // Personal Information Section
      Text('Personal Information', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
      
      TextFormField(
        decoration: InputDecoration(labelText: 'First Name *'),
        validator: (value) => value?.isEmpty ?? true ? 'Required' : null,
        onSaved: (value) => firstName = value,
      ),
      
      TextFormField(
        decoration: InputDecoration(labelText: 'Last Name'),
        onSaved: (value) => lastName = value,
      ),
      
      // Date Picker
      InkWell(
        onTap: () async {
          final date = await showDatePicker(...);
          if (date != null) dateOfBirth = date;
        },
        child: InputDecorator(
          decoration: InputDecoration(labelText: 'Date of Birth'),
          child: Text(dateOfBirth != null 
            ? DateFormat('dd/MM/yyyy').format(dateOfBirth!) 
            : 'Select Date'),
        ),
      ),
      
      // Gender Radio Buttons
      Text('Gender'),
      RadioListTile(
        title: Text('Male'),
        value: 'Male',
        groupValue: gender,
        onChanged: (value) => setState(() => gender = value),
      ),
      // Similar for Female and Other
      
      // Marital Status Dropdown
      DropdownButtonFormField<String>(
        decoration: InputDecoration(labelText: 'Marital Status'),
        value: maritalStatus,
        items: ['Single', 'Married', 'Divorced', 'Widowed', 'Other']
          .map((status) => DropdownMenuItem(value: status, child: Text(status)))
          .toList(),
        onChanged: (value) => setState(() => maritalStatus = value),
      ),
      
      SizedBox(height: 20),
      
      // Contact Information Section
      Text('Contact Information', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
      
      TextFormField(
        decoration: InputDecoration(labelText: 'Phone'),
        keyboardType: TextInputType.phone,
        onSaved: (value) => phone = value,
      ),
      
      TextFormField(
        decoration: InputDecoration(labelText: 'Address'),
        maxLines: 2,
        onSaved: (value) => address = value,
      ),
      
      TextFormField(
        decoration: InputDecoration(labelText: 'City'),
        onSaved: (value) => city = value,
      ),
      
      TextFormField(
        decoration: InputDecoration(labelText: 'State'),
        onSaved: (value) => state = value,
      ),
      
      TextFormField(
        decoration: InputDecoration(labelText: 'Post Code'),
        onSaved: (value) => postCode = value,
      ),
      
      TextFormField(
        decoration: InputDecoration(labelText: 'Country'),
        onSaved: (value) => country = value,
      ),
      
      SizedBox(height: 20),
      
      ElevatedButton(
        onPressed: () {
          if (_formKey.currentState!.validate()) {
            _formKey.currentState!.save();
            updateProfile();
          }
        },
        child: Text('Update Profile'),
      ),
    ],
  ),
)
```

## UI/UX Recommendations

1. **Form Layout:**
   - Use a scrollable form to accommodate all fields
   - Group related fields into sections with clear headings
   - Use appropriate input types (phone keyboard for phone, etc.)

2. **Validation:**
   - Show validation errors inline below each field
   - Highlight required fields with an asterisk (*)
   - Validate on field blur or form submission

3. **Date Picker:**
   - Use a native date picker for better UX
   - Display selected date in a readable format (dd/MM/yyyy)
   - Ensure date cannot be selected in the future

4. **Loading States:**
   - Show loading indicator during API call
   - Disable form submission button while processing
   - Show success/error messages after API response

5. **Error Handling:**
   - Display validation errors from API response
   - Show user-friendly error messages
   - Handle network errors gracefully

## Notes

- All fields except `first_name` are optional, but validation rules still apply if values are provided
- The API uses `post_code` in the request, but returns `zip` in the response
- Date format for API: `YYYY-MM-DD` (e.g., "1990-01-15")
- Date format for display: Can use any format (e.g., "15/01/1990" or "January 15, 1990")
- Gender and Marital Status values are case-sensitive and must match exactly: `Male`, `Female`, `Other` for gender and `Single`, `Married`, `Divorced`, `Widowed`, `Other` for marital status

