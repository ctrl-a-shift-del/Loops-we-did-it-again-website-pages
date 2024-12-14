document.addEventListener("DOMContentLoaded", function() {
    const leaderboardTable = document.getElementById('leaderboard-table');
    
    // Simulating the leaderboard data (can be fetched from backend)
    const leaderboardData = [
        { name: 'John Doe', points: 100 },
        { name: 'Jane Smith', points: 80 },
        { name: 'Alice Johnson', points: 60 },
    ];

    leaderboardData.forEach((entry, index) => {
        const row = document.createElement('tr');
        row.classList.add('text-center');
        
        const rankCell = document.createElement('td');
        rankCell.textContent = index + 1;
        row.appendChild(rankCell);
        
        const nameCell = document.createElement('td');
        nameCell.textContent = entry.name;
        row.appendChild(nameCell);
        
        const pointsCell = document.createElement('td');
        pointsCell.textContent = entry.points;
        row.appendChild(pointsCell);

        leaderboardTable.appendChild(row);
    });
});
