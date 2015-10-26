(GCode fabricated on Mon, 26 Oct 2015 23:41:17 +0000 http://davro.net)
G90 (absolute mode)
F1000  (Feed Rate)
S0  (Spindle Speed)
G21 (Metric mm)

(circle x=15 y=0 z=15 radius=10 )
G0 X5 Z15 (rapid start)
G1 Y0 (axis spindle start point)
G18 G2 X5 Z0 I10 K0.00 Y0
G0 Y10 (axis spindle safe point)
(/circle)
M2
