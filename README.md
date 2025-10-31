# AquiveMedia_DisableCustomerFileUpload

A Magento 2 security module that disables the unauthenticated customer address file upload endpoint to protect against CVE-2025-54236 (SessionReaper) and related file upload vulnerabilities.

## Security Context

### CVE-2025-54236 (SessionReaper)

In October 2025, a critical vulnerability dubbed "SessionReaper" was discovered in Magento 2 / Adobe Commerce. This vulnerability combines two attack vectors:

1. **Nested Deserialization Vulnerability** - Allows attackers to control PHP session storage paths via API deserialization chains
2. **Unauthenticated File Upload** - Permits arbitrary file uploads through the customer address endpoint

When combined, these vulnerabilities enable **remote code execution** on vulnerable Magento installations, particularly those using file-based session storage.

### The File Upload Component

Even on **patched systems** where the deserialization vulnerability has been fixed, the file upload endpoint remains a significant security risk:

**Endpoint:** `/customer/address_file/upload`

**Vulnerabilities:**
- No authentication required
- Minimal form key validation (any matching cookie/form value works)
- Allows upload of files without extensions
- Files stored in predictable locations: `pub/media/customer_address/[first_char]/[second_char]/filename`
- Can be exploited for storage abuse, XSS, and social engineering attacks
- May be chained with future vulnerabilities

## What This Module Does

This module completely disables the vulnerable file upload endpoint by intercepting all requests and returning a `403 Forbidden` response with a JSON error message.

**Implementation:**
- Uses an `around` plugin on `Magento\Customer\Controller\Address\File\Upload::execute()`
- Short-circuits the controller before any file processing occurs
- Returns a clear error message to legitimate users who might encounter it

## Installation

```bash
composer require aquivemedia/module-disable-customer-file-upload
bin/magento module:enable AquiveMedia_DisableCustomerFileUpload
bin/magento setup:upgrade
bin/magento cache:flush
```

## When to Use This Module

**Install this module if:**
- You don't use custom file upload attributes on customer addresses
- You want defense-in-depth against file upload vulnerabilities
- You want to reduce your attack surface

**You may not need this module if:**
- You actively use customer address file upload functionality (rare)
- You have custom extensions that depend on this endpoint
- You have already disabled write permissions on `pub/media/customer_address/`

## Tested on

- Magento 2.4.6-p13

## Compatibility

Should work on

- **Magento:** 2.4.x
- **Adobe Commerce:** 2.4.x
- **PHP:** 7.4+, 8.1+

## Security Recommendations

1. **Apply Adobe Security Patches:** Always install the latest security patches from Adobe
3. **Run Security Scans:** Use tools like [Sansec eComscan](https://sansec.io/ecomscan) to check for backdoors
4. **File Monitoring:** Monitor `pub/media/customer_address/` for suspicious files:
   ```bash
   find pub/media/customer_address -type f \( -name "sess_*" -o -name "*.php" \)
   ```

## References

- [CVE-2025-54236 Details](https://experienceleague.adobe.com/en/docs/experience-cloud-kcs/kbarticles/ka-27397)
- [Sansec SessionReaper Analysis](https://sansec.io/research/sessionreaper)
- [Adobe Security Bulletin APSB25-08](https://helpx.adobe.com/security/products/magento/apsb25-08.html)

## Support

For issues or questions:
- GitHub Issues: [Create an issue](https://github.com/aquivemedia/module-disable-customer-file-upload/issues)

## License

See [LICENSE](LICENSE) file for details.

## Author

- Jeroen de Reus
