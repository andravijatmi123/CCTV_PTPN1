#!/bin/bash

# CCTV Dashboard - Production Setup Script
# Run this on your production server to auto-configure everything
# 
# Usage:
#   chmod +x setup.sh
#   ./setup.sh

set -e

echo "================================"
echo "CCTV Dashboard - Production Setup"
echo "================================"
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
APP_DIR="/var/www/html/cctv"
DB_HOST="10.100.11.220"
DB_USER="cctv_user"
DB_PASS="cctv_N1HO"
DB_NAME="cctv_db"
LOG_DIR="/var/log/cctv"

echo -e "${YELLOW}Configuration:${NC}"
echo "  App Directory: $APP_DIR"
echo "  Database Host: $DB_HOST"
echo "  Database User: $DB_USER"
echo "  Log Directory: $LOG_DIR"
echo ""

# Check if running as root or with sudo
if [[ $EUID -ne 0 ]]; then
   echo -e "${RED}✗ This script must be run as root${NC}"
   exit 1
fi

echo -e "${YELLOW}Step 1: Check if application directory exists...${NC}"
if [ -d "$APP_DIR" ]; then
    echo -e "${GREEN}✓ Directory exists${NC}"
else
    echo -e "${RED}✗ Directory not found: $APP_DIR${NC}"
    exit 1
fi

echo ""
echo -e "${YELLOW}Step 2: Create .env file...${NC}"
cat > "$APP_DIR/.env" << EOF
APP_ENV=production
DB_HOST=$DB_HOST
DB_USER=$DB_USER
DB_PASS=$DB_PASS
DB_NAME=$DB_NAME
EOF

if [ -f "$APP_DIR/.env" ]; then
    echo -e "${GREEN}✓ .env file created${NC}"
    chmod 600 "$APP_DIR/.env"
    echo -e "${GREEN}✓ Set permissions to 600${NC}"
else
    echo -e "${RED}✗ Failed to create .env file${NC}"
    exit 1
fi

echo ""
echo -e "${YELLOW}Step 3: Create log directory...${NC}"
if [ ! -d "$LOG_DIR" ]; then
    mkdir -p "$LOG_DIR"
    chmod 755 "$LOG_DIR"
    echo -e "${GREEN}✓ Log directory created${NC}"
else
    echo -e "${GREEN}✓ Log directory already exists${NC}"
fi

# Change ownership based on web server
echo ""
echo -e "${YELLOW}Step 4: Set proper ownership and permissions...${NC}"
chown -R www-data:www-data "$APP_DIR"
chmod 755 "$APP_DIR"
chmod 644 "$APP_DIR"/*.php
chmod 644 "$APP_DIR"/*.css
chmod 644 "$APP_DIR"/*.js
chmod 644 "$APP_DIR"/.env

chown -R www-data:www-data "$LOG_DIR"
chmod 755 "$LOG_DIR"

echo -e "${GREEN}✓ Ownership and permissions set${NC}"

echo ""
echo -e "${YELLOW}Step 5: Test database connection...${NC}"

# Create temporary test script
TEST_SCRIPT="/tmp/test_db_$$.php"
cat > "$TEST_SCRIPT" << 'PHPEOF'
<?php
$db_host = $argv[1];
$db_user = $argv[2];
$db_pass = $argv[3];
$db_name = $argv[4];

$conn = @new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    exit(1);
} else {
    $result = $conn->query("SELECT COUNT(*) as total FROM data_cctv;");
    if ($result) {
        $row = $result->fetch_assoc();
        echo $row['total'];
    } else {
        exit(2);
    }
}
PHPEOF

# Run test
RESULT=$(php "$TEST_SCRIPT" "$DB_HOST" "$DB_USER" "$DB_PASS" "$DB_NAME" 2>/dev/null || echo "error")

if [ "$RESULT" != "error" ]; then
    echo -e "${GREEN}✓ Database connection successful${NC}"
    echo -e "${GREEN}  Total cameras in database: $RESULT${NC}"
else
    echo -e "${YELLOW}⚠ Database connection test failed${NC}"
    echo "  This might be normal if database server is on different host"
    echo "  Manually verify with:"
    echo "  mysql -h $DB_HOST -u $DB_USER -p'$DB_PASS' $DB_NAME"
fi

rm "$TEST_SCRIPT"

echo ""
echo -e "${YELLOW}Step 6: Restart web server...${NC}"

# Detect web server
if systemctl is-active --quiet nginx; then
    systemctl restart nginx
    echo -e "${GREEN}✓ Nginx restarted${NC}"
elif systemctl is-active --quiet apache2; then
    systemctl restart apache2
    echo -e "${GREEN}✓ Apache2 restarted${NC}"
else
    echo -e "${YELLOW}⚠ Could not detect or restart web server${NC}"
    echo "   Manual restart may be needed:"
    echo "   sudo systemctl restart nginx"
    echo "   or"
    echo "   sudo systemctl restart apache2"
fi

echo ""
echo "================================"
echo -e "${GREEN}✓ Setup Complete!${NC}"
echo "================================"
echo ""
echo "Next steps:"
echo "1. Open browser: http://10.100.11.220/cctv/index.php"
echo "2. Or test connection: http://10.100.11.220/cctv/test_connection.php"
echo "3. Optional - upload and run setup.php for GUI config"
echo ""
echo "Files created:"
echo "  ✓ $APP_DIR/.env"
echo "  ✓ $LOG_DIR/ (logging directory)"
echo ""
echo "To troubleshoot:"
echo "  Check error logs: tail -f $LOG_DIR/php_error.log"
echo ""
