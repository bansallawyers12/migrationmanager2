# Client Intake Form – Implementation Instructions for Website

This document describes how to build a **client intake form** on your website. When a client submits the form, the website should **generate a JSON file** that can be uploaded into the CRM. The CRM will create a **lead**, fill all mapped fields, and create an **activity note** from the form's "Notes" field.

---

## 1. Flow Overview

1. **Website:** Client fills out the intake form and submits.
2. **Website:** On submit, the front end builds a JSON object from the form fields (no server required for the form itself).
3. **Website:** Trigger download of a `.json` file (e.g. `client-intake-YYYY-MM-DD.json`) containing that JSON.
4. **CRM:** Staff upload the JSON file via **Leads → Import Lead** (on the Lead list page).
5. **CRM:** Import creates a **lead**, fills all supported fields, and creates an activity note from the **Notes** field.

> **Why import goes to Leads, not Clients:** Enquiries from the website are treated as leads first. Staff can convert a lead to a client once they're engaged.

---

## 2. How the CRM Lead Form Works (and why the import handles it differently)

The normal **Create Lead** page only collects the minimum required fields:

| Create Lead page collects | Edit Lead page adds |
|---------------------------|---------------------|
| First name, Last name | Passport information |
| Date of birth, Age, Gender | Visa information |
| Marital status | Address & Travel |
| **One** phone number | Skills & Education |
| **One** email address | Other information |
| | Family information |
| | EOI Reference |

When you import via JSON, the CRM skips the create form entirely and writes **all fields at once** directly to the database — the same result as if staff had created the lead manually and then filled in all the extra sections via the edit page. The import handles this correctly with no extra steps needed.

**Important detail — Phone Numbers & Email Addresses:** The lead edit page shows Phone Numbers and Email Addresses from dedicated linked tables (`client_contacts` and `client_emails`), **not** from the basic phone/email field on the main record. The CRM import automatically handles this: even if you only provide `client.phone` and `client.email` (the minimum), the import will create the linked records so the phone and email appear correctly on the edit page. You do **not** need to repeat them in the `contacts`/`emails` arrays unless you have additional numbers or addresses to add.

---

## 3. JSON Structure Required by CRM

The CRM import expects a single JSON object with the following top-level keys. Only `client` is required; all others are optional.

| Top-level key    | Description |
|------------------|-------------|
| `client`         | **Required.** Object with basic personal details. |
| `contacts`       | Optional. Array of **extra** phone numbers beyond the primary. |
| `emails`         | Optional. Array of **extra** email addresses beyond the primary. |
| `passport`       | Optional. Single passport object. |
| `visa_countries` | Optional. Array of visa entries. |
| `addresses`      | Optional. Array of address entries. |
| `notes`          | Optional. **Free-text notes.** Creates an activity note in the CRM. |

---

## 4. Form Sections and Field Mapping

### 4.1 Basic Information (inside `client` — required minimum)

| Form label        | JSON key (`client.*`)  | Required | Notes |
|-------------------|------------------------|----------|-------|
| First name        | `first_name`           | **Yes**  | |
| Last name         | `last_name`            | No       | |
| Email             | `email`                | **Yes**  | Used for duplicate check. Must be unique. |
| Phone             | `phone`                | No       | Digits only; CRM also checks for duplicates on this. |
| Country code      | `country_code`         | No       | e.g. `+61`, `+91` |
| Date of birth     | `dob`                  | No       | **YYYY-MM-DD** or **DD/MM/YYYY** |
| Age               | `age`                  | No       | Can be calculated from DOB or omitted |
| Gender            | `gender`               | No       | `Male`, `Female`, or `Other` |
| Marital status    | `marital_status`       | No       | `Never Married`, `Married`, `De Facto`, `Divorced`, `Widowed`, `Separated`, `Engaged` |

**Minimum required:** `client.first_name` and `client.email` must be non-empty.

> The `client.phone` and `client.email` values are automatically saved into the Phone Numbers and Email Addresses sections of the lead edit page. You do **not** need to duplicate them in the `contacts` / `emails` arrays.

### 4.2 Extra Phone Numbers (optional — `contacts` array)

Only needed if the client has **more than one** phone number. The primary phone goes in `client.phone`.

```json
"contacts": [
  {
    "contact_type": "Work",
    "country_code": "+61",
    "phone": "0298765432"
  }
]
```

| Key            | Notes |
|----------------|-------|
| `contact_type` | e.g. `Personal`, `Work`. Defaults to `Personal` if omitted. |
| `country_code` | e.g. `+61` |
| `phone`        | Digits (with or without spaces). Entries with empty `phone` are skipped. |

### 4.3 Extra Email Addresses (optional — `emails` array)

Only needed if the client has **more than one** email address. The primary email goes in `client.email`.

```json
"emails": [
  {
    "email_type": "Work",
    "email": "jane.smith@company.com"
  }
]
```

| Key          | Notes |
|--------------|-------|
| `email_type` | e.g. `Personal`, `Work`. Defaults to `Personal` if omitted. |
| `email`      | Valid email address. Entries with empty `email` are skipped. |

### 4.4 Passport (optional — `passport` object)

One passport per import:

```json
"passport": {
  "passport_country": "India",
  "passport_number": "N1234567",
  "passport_issue_date": "2020-01-15",
  "passport_expiry_date": "2030-01-14"
}
```

| Key                    | Notes |
|------------------------|-------|
| `passport_country`     | Country name (e.g. `India`, `Australia`) |
| `passport_number`      | Passport number. `passport` is also accepted as an alias. |
| `passport_issue_date`  | **YYYY-MM-DD** or **DD/MM/YYYY** |
| `passport_expiry_date` | **YYYY-MM-DD** or **DD/MM/YYYY** |

### 4.5 Visa Information (optional — `visa_countries` array)

```json
"visa_countries": [
  {
    "visa_type_matter_nick_name": "485",
    "visa_expiry_date": "2026-06-30",
    "visa_grant_date": "2024-01-15",
    "visa_description": "Temporary Graduate – Post-study work"
  }
]
```

| Key                          | Notes |
|------------------------------|-------|
| `visa_type_matter_nick_name` | **Preferred.** CRM looks up the Matter by nickname (e.g. `485`, `189`, `190`). |
| `visa_type_matter_title`     | Alternative. CRM looks up the Matter by full title. |
| `visa_expiry_date`           | **YYYY-MM-DD** or **DD/MM/YYYY** |
| `visa_grant_date`            | Optional. Same date formats. |
| `visa_description`           | Optional free text. |

> If `visa_countries` is omitted and you just know the visa type label, you can put it on the client object: `client.visa_type` (text label), `client.visa_expiry` (date), `client.visa_opt` (description). The CRM will still create a visa record.

### 4.6 Address (optional — `addresses` array)

```json
"addresses": [
  {
    "address_line_1": "123 Main St",
    "suburb": "Sydney",
    "state": "NSW",
    "country": "Australia",
    "zip": "2000",
    "is_current": 1
  }
]
```

| Key              | Notes |
|------------------|-------|
| `address_line_1` | Street address |
| `address_line_2` | Optional second line |
| `suburb`         | Suburb or city. `city` also accepted. |
| `state`          | State or region |
| `country`        | Country name |
| `zip`            | Postcode |
| `is_current`     | `1` = current address, `0` = past |

### 4.7 Notes → Activity Note (strongly recommended)

| Form element       | JSON key (top-level) | CRM behaviour |
|--------------------|----------------------|---------------|
| Notes / textarea   | `notes`              | CRM creates an **activity note** with subject *"Lead intake – additional information"* and the notes text as the body. Staff see it immediately in the activity feed. |

- Use a `<textarea>` on the form labelled "Additional information / notes".
- Trim whitespace before writing to JSON; if the trimmed value is empty, omit the key.
- The note is **always created** when `notes` is present — even if the JSON also contains other activity data.

---

## 5. Full Minimal Example (phone + email only)

The simplest valid JSON the CRM will accept:

```json
{
  "client": {
    "first_name": "Jane",
    "last_name": "Smith",
    "email": "jane.smith@example.com",
    "phone": "412345678",
    "country_code": "+61",
    "dob": "1990-05-15",
    "gender": "Female",
    "marital_status": "Married"
  },
  "notes": "Client is enquiring about a 485 visa. Prefers contact by email."
}
```

The CRM will create the lead and automatically ensure the phone and email appear in the Phone Numbers and Email Addresses sections of the edit page.

---

## 6. Full Example with All Optional Sections

```json
{
  "client": {
    "first_name": "Jane",
    "last_name": "Smith",
    "email": "jane.smith@example.com",
    "phone": "412345678",
    "country_code": "+61",
    "dob": "1990-05-15",
    "gender": "Female",
    "marital_status": "Married"
  },
  "contacts": [
    {
      "contact_type": "Work",
      "country_code": "+61",
      "phone": "0298765432"
    }
  ],
  "emails": [
    {
      "email_type": "Work",
      "email": "jane.smith@company.com"
    }
  ],
  "passport": {
    "passport_country": "India",
    "passport_number": "N1234567",
    "passport_issue_date": "2020-01-15",
    "passport_expiry_date": "2030-01-14"
  },
  "visa_countries": [
    {
      "visa_type_matter_nick_name": "485",
      "visa_expiry_date": "2026-06-30",
      "visa_grant_date": "2024-01-15",
      "visa_description": "Temporary Graduate"
    }
  ],
  "addresses": [
    {
      "address_line_1": "123 Main St",
      "suburb": "Sydney",
      "state": "NSW",
      "country": "Australia",
      "zip": "2000",
      "is_current": 1
    }
  ],
  "notes": "Client will provide certified qualification copies next week. Enquiring about 485 to 189 pathway."
}
```

In this example, `contacts` and `emails` contain **extra** numbers/addresses. The primary phone (`412345678`) and primary email (`jane.smith@example.com`) from the `client` object are also saved automatically.

---

## 7. Date and Format Rules

- **Dates:** Use **YYYY-MM-DD** or **DD/MM/YYYY**. The CRM parses both.
- **Phone:** Store digits only (e.g. `412345678`); put `+61` in `country_code`.
- **Empty values:** Omit the key or send `null`; the CRM treats both as "not set".
- **File encoding:** UTF-8. No trailing commas. All keys must be quoted strings.

---

## 8. Validation on the Website

Recommended client-side checks before generating the file:

- **Required:** `client.first_name` and `client.email` must be non-empty.
- **Email format:** Basic `@` check for `client.email`.
- **Date format:** Validate or normalise DOB, passport dates, and visa dates.
- **Notes:** Trim whitespace; omit the key if empty.
- **Passport:** Only include the `passport` key if at least `passport_number` or `passport_expiry_date` was entered.
- **Visa:** Only include `visa_countries` if at least one visa entry has a type or expiry date.

---

## 9. How Staff Import in the CRM

1. Go to **Leads** in the CRM.
2. Click **Import Lead** (top-right of the lead list).
3. Select the `.json` file downloaded from the website form.
4. Leave **"Skip if lead with same email or phone already exists"** checked (recommended) to avoid duplicates.
5. Click **Import Lead**.

**What the CRM creates:**
- A new **lead** record with all basic fields filled.
- Phone Numbers and Email Addresses sections populated (visible on the lead edit page).
- Passport, Visa, Address records if those sections were in the JSON.
- One **activity note** (visible in the activity feed) from the `notes` field.

---

## 10. Summary Checklist for Your Form

- [ ] Collects at least **first name** and **email** (required).
- [ ] Has a **Notes** textarea → written to root-level `"notes"` in JSON.
- [ ] Primary phone → `client.phone` + `client.country_code`.
- [ ] Primary email → `client.email`.
- [ ] Extra phones (if any) → `contacts` array only (don't repeat the primary).
- [ ] Extra emails (if any) → `emails` array only (don't repeat the primary).
- [ ] Dates in **YYYY-MM-DD** or **DD/MM/YYYY**.
- [ ] On submit, builds JSON and triggers a `.json` file download (no server needed).
- [ ] Staff upload via **Leads → Import Lead**.
