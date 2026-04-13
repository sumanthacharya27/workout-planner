<!-- Calculator Section -->
<div id="calculator" class="page-section">
    <h1>Body Calculator</h1>
    <p class="subtitle">Calculate your BMI and Basal Metabolic Rate</p>

    <div class="admin-tabs" style="margin-bottom: 2rem;">
        <button class="admin-tab-btn active" data-calc-tab="bmiTab">BMI Calculator</button>
        <button class="admin-tab-btn" data-calc-tab="bmrTab">BMR Calculator</button>
    </div>

    <!-- BMI Tab -->
    <div id="bmiTab" class="admin-tab-content active">
        <div class="workout-form">
            <div class="form-row">
                <div class="form-group">
                    <label>Gender</label>
                    <select id="bmiGender">
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Age</label>
                    <input type="number" id="bmiAge" min="2" max="120" placeholder="e.g. 25">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Height (cm)</label>
                    <input type="number" id="bmiHeight" min="50" max="300" placeholder="e.g. 175">
                </div>
                <div class="form-group">
                    <label>Weight (kg)</label>
                    <input type="number" id="bmiWeight" min="1" max="500" placeholder="e.g. 70">
                </div>
            </div>
            <button class="btn-save btn-primary" onclick="calculateBMI()">Calculate BMI</button>
        </div>

        <div id="bmiResult" style="display:none; margin-top:2rem;">
            <div class="dashboard-stats" style="margin-bottom:1.5rem;">
                <div class="stat-card">
                    <div class="stat-icon">⚖️</div>
                    <div class="stat-number" id="bmiValue">-</div>
                    <div class="stat-label">Your BMI</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" id="bmiCategoryIcon">✅</div>
                    <div class="stat-number" id="bmiCategory" style="font-size:1.4rem;">-</div>
                    <div class="stat-label">Category</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📏</div>
                    <div class="stat-number" id="bmiHealthyRange" style="font-size:1.2rem;">-</div>
                    <div class="stat-label">Healthy Weight (kg)</div>
                </div>
            </div>

            <div class="workout-form">
                <label style="font-weight:700; color:var(--dark); text-transform:uppercase; font-size:0.85rem; letter-spacing:0.5px;">BMI Scale</label>
                <div style="margin-top:0.75rem; border-radius:50px; overflow:hidden; height:18px; display:flex;">
                    <div style="flex:1; background:#1B98E0;" title="Underweight"></div>
                    <div style="flex:2; background:#06D6A0;" title="Normal"></div>
                    <div style="flex:1.5; background:#F7931E;" title="Overweight"></div>
                    <div style="flex:2; background:#EF476F;" title="Obese"></div>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:0.78rem; color:var(--gray-700); margin-top:4px; font-weight:600;">
                    <span>Underweight</span><span>Normal</span><span>Overweight</span><span>Obese</span>
                </div>
                <div style="position:relative; height:28px; margin-top:4px;">
                    <div id="bmiMarker" style="position:absolute; transform:translateX(-50%); background:var(--dark); color:#fff; font-size:0.75rem; font-weight:700; padding:3px 8px; border-radius:20px; white-space:nowrap;">▲ You</div>
                </div>
                <p id="bmiAdvice" style="margin-top:1rem; color:var(--gray-700); line-height:1.7; background:var(--gray-100); padding:1.25rem; border-radius:12px; border-left:4px solid var(--primary);"></p>
            </div>
        </div>
    </div>

    <!-- BMR Tab -->
    <div id="bmrTab" class="admin-tab-content">
        <div class="workout-form">
            <div class="form-row">
                <div class="form-group">
                    <label>Gender</label>
                    <select id="bmrGender">
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Age</label>
                    <input type="number" id="bmrAge" min="1" max="120" placeholder="e.g. 25">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Height (cm)</label>
                    <input type="number" id="bmrHeight" placeholder="e.g. 175">
                </div>
                <div class="form-group">
                    <label>Weight (kg)</label>
                    <input type="number" id="bmrWeight" placeholder="e.g. 70">
                </div>
            </div>
            <div class="form-group">
                <label>Activity Level</label>
                <select id="bmrActivity">
                    <option value="1.2">Sedentary (little or no exercise)</option>
                    <option value="1.375">Lightly Active (1–3 days/week)</option>
                    <option value="1.55" selected>Moderately Active (3–5 days/week)</option>
                    <option value="1.725">Very Active (6–7 days/week)</option>
                    <option value="1.9">Extra Active (physical job or 2x/day training)</option>
                </select>
            </div>
            <button class="btn-save btn-primary" onclick="calculateBMR()">Calculate BMR</button>
        </div>

        <div id="bmrResult" style="display:none; margin-top:2rem;">
            <div class="dashboard-stats" style="margin-bottom:1.5rem;">
                <div class="stat-card">
                    <div class="stat-icon">🔥</div>
                    <div class="stat-number" id="bmrValue">-</div>
                    <div class="stat-label">BMR (kcal/day)</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">⚡</div>
                    <div class="stat-number" id="tdeeValue">-</div>
                    <div class="stat-label">TDEE (kcal/day)</div>
                </div>
            </div>
            <div class="workout-form">
                <label style="font-weight:700; color:var(--dark); text-transform:uppercase; font-size:0.85rem; letter-spacing:0.5px; display:block; margin-bottom:1rem;">Daily Calorie Goals</label>
                <div class="exercises-list" id="bmrGoals"></div>
            </div>
        </div>
    </div>
</div>