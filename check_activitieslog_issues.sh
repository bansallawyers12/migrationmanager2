#!/bin/bash

# Script to find ActivitiesLog instances that might be missing task_status and pin
# Run this from the project root directory

echo "=== Checking for ActivitiesLog instances missing task_status/pin ==="
echo ""

# Find all files with "new ActivitiesLog"
echo "1. Finding all files with 'new ActivitiesLog' pattern:"
echo "---------------------------------------------------"
grep -rn "new ActivitiesLog" app/ --include="*.php" | grep -v "task_status\|pin" | head -20
echo ""

# Find all files with "new ActivitiesLog" that have save() nearby
echo "2. Finding 'new ActivitiesLog' followed by save() (potential issues):"
echo "--------------------------------------------------------------------"
grep -rn -A 10 "new ActivitiesLog" app/ --include="*.php" | grep -B 5 "->save()" | grep -v "task_status\|pin" | head -30
echo ""

# More specific: Find lines with "new ActivitiesLog" and check if task_status/pin appear within 15 lines
echo "3. Files with 'new ActivitiesLog' that might be missing task_status/pin:"
echo "-----------------------------------------------------------------------"
for file in $(grep -rl "new ActivitiesLog" app/ --include="*.php"); do
    # Check if file has ActivitiesLog with save() but no task_status/pin in nearby lines
    if grep -A 15 "new ActivitiesLog" "$file" | grep -q "->save()"; then
        if ! grep -A 15 "new ActivitiesLog" "$file" | grep -q "task_status\|pin"; then
            echo "⚠️  POTENTIAL ISSUE: $file"
            grep -n -A 10 "new ActivitiesLog" "$file" | head -15
            echo ""
        fi
    fi
done

echo ""
echo "=== Done ==="
echo ""
echo "Manual check: Review each file above and verify task_status and pin are set before save()"
