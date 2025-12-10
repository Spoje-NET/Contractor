# DOCX Template Guide

This guide explains how to create, edit, and use DOCX templates for the Contractor application.

## Overview
Contractor uses **Mustache** syntax to inject dynamic data into your Word Documents (`.docx`). You can simply open these files in Microsoft Word (or LibreOffice, Google Docs, etc.), type your placeholders, and save.

## How to Edit Templates

1.  **Open Word**: Create a new blank document or open an existing one.
2.  **Add Placeholders**: Type double curly braces `{{ }}` around variable names.
3.  **Save**: Save the file as a `.docx` in the `templates/` directory of the application.

> **Tip**: Contractor includes a "smart merge" feature. If Word splits your `{{variable}}` into multiple XML tags (formatting breaks), the system will attempt to repair it automatically. However, for best results, avoid changing fonts or colors *in the middle* of a variable name.

## Syntax Examples

### 1. Variables (Simple Text)
Use variables to insert data fields. Dotted notation allows accessing nested data.

**Template:**
> Contract Number: `{{contract.kod}}`
> Client Name: `{{contract.firma.nazev}}`
> Signed Date: `{{contract.datumPodepsani}}`

**Result:**
> Contract Number: CON-2024-001
> Client Name: ACME Corp
> Signed Date: 12.10.2024

---

### 2. Conditions (If/Else)
You can show or hide blocks of text based on whether a value exists or is true.

**Syntax:** `{{#variable}} ... {{/variable}}`

**Template:**
```text
{{#contract.vatRegistered}}
   Client is VAT Registered. VAT ID: {{contract.vatId}}
{{/contract.vatRegistered}}
```

**Behavior:**
- If `contract.vatRegistered` is true (or a non-empty string/number), the text inside is shown.
- If it is false or empty, the text is removed.

---

### 3. Cycles (Loops)
Use the same syntax `{{#variable}} ... {{/variable}}` to iterate over lists of items. The content inside the block is repeated for each item. Inside the block, the context switches to the item itself.

**Template:**
```text
Services Provided:
{{#contract.polozkySmlouvy}}
 - {{nazev}}: {{cenaZakl}} CZK ({{kratkyPopis}})
{{/contract.polozkySmlouvy}}
```

**Data Structure (Example):**
```json
"polozkySmlouvy": [
    {"nazev": "Internet 100Mb", "cenaZakl": 500, "kratkyPopis": "High speed connection"},
    {"nazev": "Public IP", "cenaZakl": 100, "kratkyPopis": "Static IPv4"}
]
```

**Result:**
> Services Provided:
>  - Internet 100Mb: 500 CZK (High speed connection)
>  - Public IP: 100 CZK (Static IPv4)

---

### 4. Advanced: Accessing Parent Data in Loops
If you are inside a loop (e.g., iterating items) and need to access data from the main contract (e.g., Contract Code), you can still reference it if it's passed into the scope.

*Note: In the current MiniMustache implementation, scopes are merged. The item properties override root properties, but root properties are still accessible if the item doesn't have a property with the same name.*

**Example:**
```text
{{#contract.polozkySmlouvy}}
   Item for contract {{contract.kod}}: {{nazev}}
{{/contract.polozkySmlouvy}}
```

---

## Available Data Fields
The template receives the `Contract` object data. Common fields include:

- **contract**
    - `kod`: Contract Code
    - `datumPodepsani`: Date Signed
    - **firma** (Company/Client)
        - `nazev`: Name
        - `ic`: Registration Number
        - `dic`: VAT Number
        - **kontakt** (Primary Contact)
            - `jmeno`: First Name
            - `prijmeni`: Last Name
            - `email`: Email
            - `tel`: Phone
    - **polozkySmlouvy** (Items Array)
        - `nazev`
        - `cenaZakl`
        - `szbDph`

## Troubleshooting
- **Tags showing as plain text?** Ensure you used double curly braces `{{ }}`.
- **Tags broken/not rendering?** If you see `{{ variable }}` literally in the output but expected a value, check if the variable name is correct. If you see partial tags like `{{` ... `}}` with weird stuff in between in the XML (rare), try re-typing the tag in Word cleanly.
