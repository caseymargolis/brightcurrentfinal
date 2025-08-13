# Solar Monitoring System - Client Usage Guide

## Overview

This solar monitoring system enables solar companies to track and monitor their customers' solar installations from multiple vendors (SolarEdge, Enphase, Tesla). The system provides real-time monitoring, downtime detection, weather correlation, and automated reporting.

## Table of Contents

1. [Getting Started](#getting-started)
2. [API Authentication Setup](#api-authentication-setup)
3. [System Configuration](#system-configuration)
4. [Daily Operations](#daily-operations)
5. [Customer Management](#customer-management)
6. [Troubleshooting](#troubleshooting)
7. [Advanced Features](#advanced-features)

## Getting Started

### Prerequisites

- Access to your customers' solar monitoring portals
- Admin access to solar vendor accounts (SolarEdge, Enphase, Tesla)
- Basic technical knowledge for API key setup

### Initial Setup Checklist

- [ ] Obtain API credentials from solar vendors
- [ ] Configure system with customer sites
- [ ] Test API connections
- [ ] Set up automated monitoring
- [ ] Configure alert preferences

## API Authentication Setup

### 1. SolarEdge API Setup

**Step 1: Access SolarEdge Monitoring Portal**
1. Log into [SolarEdge Monitoring Portal](https://monitoring.solaredge.com/)
2. Ensure you have admin access to customer sites

**Step 2: Generate API Key**
1. Click on the "Admin" tab (gear icon)
2. Navigate to "Site Access" → "Access Control" tab
3. Scroll to "API Access" section
4. Check the Terms & Conditions box
5. Click "New Key" to generate API key
6. Click "Save" and copy the API key

**Step 3: Get Site IDs**
```bash
# List all accessible sites
php artisan solaredge:list-sites

# Get detailed information for a specific site
php artisan solaredge:site-details {site_id}
```

**Configuration:**
Add to your `.env` file:
```
SOLAREDGE_API_KEY=your_api_key_here
```

### 2. Enphase API Setup (OAuth Required)

**Step 1: Developer Account Setup**
1. Register at [Enphase Developer Portal](https://developer-v4.enphase.com/)
2. Create a Partner application (requires registered installer status)
3. Note down: Client ID, Client Secret, API Key

**Step 2: Authentication**
```bash
# Authenticate with your Enphase Enlighten credentials
php artisan enphase:authenticate --username=your_email --password=your_password
```

**Configuration:**
Add to your `.env` file:
```
ENPHASE_API_KEY=your_api_key
ENPHASE_CLIENT_ID=your_client_id
ENPHASE_CLIENT_SECRET=your_client_secret
```

### 3. Tesla API Setup (Optional)

Tesla API integration requires OAuth 2.0 setup. Currently in development.

**Configuration:**
Add to your `.env` file:
```
TESLA_CLIENT_ID=your_client_id
TESLA_CLIENT_SECRET=your_client_secret
```

### 4. Weather API Setup

Weather API is already configured and working. No additional setup required.

## System Configuration

### Adding Customer Systems

1. **Access Database:**
   - Systems are stored in the `systems` table
   - Each system requires: name, manufacturer, external_system_id, capacity, location

2. **Required Information per System:**
   - **SolarEdge:** Site ID (from monitoring portal)
   - **Enphase:** System ID (from Enlighten portal)
   - **Tesla:** Energy Site ID (from Tesla app)

3. **System Status Options:**
   - `active` - System is operational
   - `inactive` - System is offline
   - `maintenance` - System under maintenance

### Database Schema

**Systems Table:**
```sql
- system_id (UUID, Primary Key)
- name (Customer/Site name)
- manufacturer (solaredge/enphase/tesla)
- external_system_id (Site ID from vendor)
- capacity (System capacity in kW)
- location (Installation location)
- status (active/inactive/maintenance)
- installed_date
- api_enabled (boolean)
```

## Daily Operations

### Monitoring Commands

**Test API Connections:**
```bash
# Test all APIs
php artisan solar:test-apis

# Test specific API
php artisan solar:test-apis solaredge
php artisan solar:test-apis enphase
php artisan solar:test-apis weather
```

**Data Synchronization:**
```bash
# Sync all systems data
php artisan solar:sync-all-data

# Process background jobs
php artisan solar:process-queue

# Generate daily report
php artisan solar:daily-report

# Monitor system health
php artisan solar:monitor-health
```

### Automated Tasks

The system automatically runs these tasks via Laravel's scheduler:

- **Every 15 minutes:** Sync solar production data
- **Every hour:** Process health monitoring
- **Daily at 8 AM:** Generate daily reports
- **Daily at 6 AM:** System health checks

### Dashboard Access

**Public Landing Page:** Available at your application URL
- Displays aggregated metrics
- Shows system status overview
- No authentication required

**Admin Dashboard:** Access via `/dashboard` (requires authentication)
- Detailed system metrics
- Individual system performance
- Alert management
- Historical data analysis

## Customer Management

### Adding New Customers

1. **Obtain System Information:**
   - Solar vendor (SolarEdge/Enphase/Tesla)
   - System capacity (kW)
   - Installation location
   - Installation date

2. **Get External System ID:**
   - **SolarEdge:** Use `php artisan solaredge:list-sites`
   - **Enphase:** Check Enlighten portal
   - **Tesla:** Check Tesla app

3. **Add to Database:**
   ```sql
   INSERT INTO systems (system_id, name, manufacturer, external_system_id, capacity, location, status, api_enabled)
   VALUES (UUID(), 'Customer Name', 'solaredge', 'site_id', 10.5, 'City, State', 'active', true);
   ```

### Customer Support Scenarios

**Scenario 1: Customer Reports Low Production**
1. Check system status: `php artisan solar:test-apis`
2. Review recent production data in dashboard
3. Check weather conditions for location
4. Verify system is marked as 'active'

**Scenario 2: System Shows Offline**
1. Verify API connectivity
2. Check if external system ID is correct
3. Confirm customer's monitoring portal access
4. Review system alerts table

**Scenario 3: New Installation**
1. Obtain all required system information
2. Test API access to new system
3. Add system to database
4. Run sync to populate initial data
5. Monitor for 24 hours to ensure proper operation

## Troubleshooting

### Common API Issues

**SolarEdge "403 Forbidden" Error:**
- API key is invalid or expired
- Account doesn't have admin access
- Site ID is incorrect
- **Solution:** Regenerate API key, verify admin access

**Enphase "401 Unauthorized" Error:**
- Access token expired
- OAuth credentials incorrect
- Account permissions insufficient
- **Solution:** Re-run `php artisan enphase:authenticate`

**Weather API Issues:**
- Usually indicates API key problems
- Check WEATHER_API_KEY in .env file
- Verify WeatherAPI.com account status

### Performance Issues

**Slow Data Sync:**
- Check API rate limits
- Verify internet connectivity
- Review background job queue status
- Consider reducing sync frequency

**Missing Data:**
- Verify system external_system_id is correct
- Check API permissions for specific sites
- Review error logs for specific failures

### System Maintenance

**Regular Maintenance Tasks:**
- Monitor API key expiration dates
- Review system performance metrics
- Update customer system information
- Clean up old alert records
- Verify backup procedures

## Advanced Features

### Custom Reporting

Create custom reports by querying the production data:

```sql
-- Daily production by manufacturer
SELECT manufacturer, SUM(energy_today) as total_energy
FROM production_data p
JOIN systems s ON p.system_id = s.system_id
WHERE date(p.date) = CURRENT_DATE
GROUP BY manufacturer;

-- System efficiency rankings
SELECT s.name, AVG(p.efficiency) as avg_efficiency
FROM production_data p
JOIN systems s ON p.system_id = s.system_id
WHERE p.date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY s.system_id
ORDER BY avg_efficiency DESC;
```

### Alert Configuration

Customize alert thresholds in the system health monitoring:

- Low production alerts (< 50% expected)
- System offline alerts (no data > 2 hours)
- Efficiency degradation alerts (< 80% capacity)
- Weather-related performance alerts

### API Rate Limit Management

**Current Limits:**
- SolarEdge: 300 requests per day per API key
- Enphase: Varies by subscription plan
- Weather API: 1,000,000 requests per month

**Best Practices:**
- Sync data every 15 minutes (not more frequently)
- Use batch requests when possible
- Monitor rate limit headers in responses
- Implement exponential backoff for retries

### Data Export

Export data for external analysis:

```bash
# Export production data to CSV
php artisan solar:export-data --format=csv --date-range="2024-01-01,2024-01-31"

# Export system summary
php artisan solar:export-systems --format=json
```

## Support and Contact

### Technical Support

For technical issues with the monitoring system:
1. Check this guide first
2. Review system logs: `tail -f storage/logs/laravel.log`
3. Test API connections: `php artisan solar:test-apis`
4. Contact your system administrator

### Vendor Support

For issues with solar vendor APIs:
- **SolarEdge:** [SolarEdge Support](https://www.solaredge.com/support)
- **Enphase:** [Enphase Support](https://support.enphase.com/)
- **Tesla:** [Tesla Support](https://www.tesla.com/support)

### Emergency Procedures

**System Down:**
1. Check supervisor status: `supervisorctl status`
2. Restart services: `supervisorctl restart all`
3. Check database connectivity
4. Review error logs

**Data Loss:**
1. Check backup status
2. Review database integrity
3. Re-sync recent data: `php artisan solar:sync-all-data`

---

*Last Updated: December 2024*
*Version: 1.0*