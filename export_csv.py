import pymysql
import csv

def export_members_to_csv():
    """Export members table to CSV"""
    try:
        # Connect to database
        connection = pymysql.connect(
            host="localhost",
            port=3306,
            user="root",
            password="",
            database="crud_app"
        )
        
        cursor = connection.cursor()
        
        # Get all members
        cursor.execute("SELECT * FROM members")
        members = cursor.fetchall()
        
        # Get column names
        cursor.execute("SHOW COLUMNS FROM members")
        columns = [column[0] for column in cursor.fetchall()]
        
        # Write to CSV
        with open('members_export.csv', 'w', newline='') as csvfile:
            writer = csv.writer(csvfile)
            writer.writerow(columns)
            writer.writerows(members)
        
        print(f"Exported {len(members)} members to members_export.csv")
        
        cursor.close()
        connection.close()
        return True
        
    except Exception as e:
        print(f"Error: {e}")
        return False

if __name__ == "__main__":
    export_members_to_csv()