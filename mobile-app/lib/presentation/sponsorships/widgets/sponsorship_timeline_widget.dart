import 'package:flutter/material.dart';

import '../../../core/localization/app_strings.dart';
import '../../../core/theme/app_colors.dart';
import '../../../core/theme/app_typography.dart';

class SponsorshipTimelineWidget extends StatelessWidget {
  final String currentStatus;
  final DateTime? startDate;

  const SponsorshipTimelineWidget({
    super.key,
    required this.currentStatus,
    this.startDate,
  });

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final stages = _buildStages();

    return Container(
      width: double.infinity,
      padding: const EdgeInsets.symmetric(vertical: 24, horizontal: 8),
      decoration: BoxDecoration(
        color: isDark ? AppColors.darkCard : Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(
          color: isDark
              ? Colors.white.withOpacity(0.08)
              : AppColors.neutral200,
        ),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16),
            child: Text(
              AppStrings.get('sponsorship_path'),
              style: AppTypography.titleMedium.copyWith(
                color: isDark ? Colors.white : AppColors.neutral900,
              ),
            ),
          ),
          const SizedBox(height: 24),
          ...List.generate(stages.length, (index) {
            final stage = stages[index];
            final isFirst = index == 0;
            final isLast = index == stages.length - 1;
            return _TimelineNode(
              stage: stage,
              isFirst: isFirst,
              isLast: isLast,
            );
          }),
        ],
      ),
    );
  }

  List<_TimelineStage> _buildStages() {
    if (currentStatus == 'rejected') {
      return [
        _TimelineStage(
          icon: Icons.check,
          label: AppStrings.get('request_sent'),
          state: _NodeState.completed,
        ),
        _TimelineStage(
          icon: Icons.cancel,
          label: AppStrings.get('request_rejected'),
          state: _NodeState.error,
        ),
      ];
    }

    if (currentStatus == 'cancelled') {
      return [
        _TimelineStage(
          icon: Icons.check,
          label: AppStrings.get('request_sent'),
          state: _NodeState.completed,
        ),
        _TimelineStage(
          icon: Icons.check,
          label: AppStrings.get('request_approved'),
          state: _NodeState.completed,
        ),
        _TimelineStage(
          icon: Icons.block,
          label: AppStrings.get('sponsorship_cancelled'),
          state: _NodeState.skipped,
        ),
      ];
    }

    const approvedStatuses = ['active', 'inactive', 'ended'];
    final isApproved = approvedStatuses.contains(currentStatus);
    final isActive = currentStatus == 'active';
    final isCompleted = currentStatus == 'ended';

    return [
      _TimelineStage(
        icon: Icons.check,
        label: AppStrings.get('request_sent'),
        date: startDate != null
            ? '${startDate!.year}-${startDate!.month.toString().padLeft(2, '0')}-${startDate!.day.toString().padLeft(2, '0')}'
            : null,
        state: _NodeState.completed,
      ),
      _TimelineStage(
        icon: isApproved ? Icons.check : Icons.hourglass_empty,
        label: AppStrings.get('request_approved'),
        date: null,
        state: isApproved ? _NodeState.completed : _NodeState.active,
      ),
      _TimelineStage(
        icon: isActive || isCompleted ? Icons.check : Icons.hourglass_empty,
        label: AppStrings.get('sponsorship_started'),
        date: null,
        state: isActive || isCompleted ? _NodeState.completed : _NodeState.inactive,
      ),
      _TimelineStage(
        icon: isCompleted
            ? Icons.check
            : isActive
                ? Icons.hourglass_empty
                : Icons.circle_outlined,
        label: AppStrings.get('sponsorship_active'),
        date: null,
        state: isCompleted
            ? _NodeState.completed
            : isActive
                ? _NodeState.active
                : _NodeState.inactive,
      ),
      if (isCompleted)
        _TimelineStage(
          icon: Icons.check,
          label: AppStrings.get('ended'),
          date: null,
          state: _NodeState.completed,
        ),
    ];
  }
}

enum _NodeState { completed, active, inactive, error, skipped }

class _TimelineStage {
  final IconData icon;
  final String label;
  final String? date;
  final _NodeState state;

  const _TimelineStage({
    required this.icon,
    required this.label,
    this.date,
    required this.state,
  });
}

class _TimelineNode extends StatelessWidget {
  final _TimelineStage stage;
  final bool isFirst;
  final bool isLast;

  const _TimelineNode({
    required this.stage,
    required this.isFirst,
    required this.isLast,
  });

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;

    Color nodeColor;
    Color lineColor;
    Widget iconWidget;

    switch (stage.state) {
      case _NodeState.completed:
        nodeColor = AppColors.success;
        lineColor = AppColors.success;
        iconWidget = const Icon(Icons.check, color: Colors.white, size: 16);
      case _NodeState.active:
        nodeColor = AppColors.warning;
        lineColor = Colors.grey;
        iconWidget = Icon(stage.icon, color: Colors.white, size: 16);
      case _NodeState.inactive:
        nodeColor = isDark ? Colors.grey[700]! : AppColors.neutral300;
        lineColor = Colors.grey;
        iconWidget = Icon(stage.icon, color: Colors.white, size: 16);
      case _NodeState.error:
        nodeColor = AppColors.error;
        lineColor = AppColors.error;
        iconWidget = const Icon(Icons.close, color: Colors.white, size: 16);
      case _NodeState.skipped:
        nodeColor = isDark ? Colors.grey[600]! : AppColors.neutral400;
        lineColor = Colors.grey;
        iconWidget = const Icon(Icons.block, color: Colors.white, size: 16);
    }

    return IntrinsicHeight(
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          SizedBox(
            width: 64,
            child: Column(
              children: [
                if (!isFirst)
                  Expanded(
                    child: Container(
                      width: 3,
                      color: lineColor.withOpacity(0.4),
                    ),
                  ),
                Container(
                  width: 40,
                  height: 40,
                  decoration: BoxDecoration(
                    color: nodeColor,
                    shape: BoxShape.circle,
                    boxShadow: stage.state == _NodeState.active
                        ? [
                            BoxShadow(
                              color: nodeColor.withOpacity(0.4),
                              blurRadius: 10,
                              spreadRadius: 1,
                            ),
                          ]
                        : null,
                  ),
                  child: stage.state == _NodeState.active
                      ? _PulseWrapper(child: iconWidget)
                      : iconWidget,
                ),
                if (!isLast)
                  Expanded(
                    child: Container(
                      width: 3,
                      color: lineColor.withOpacity(0.4),
                    ),
                  ),
              ],
            ),
          ),
          const SizedBox(width: 8),
          Expanded(
            child: Padding(
              padding: EdgeInsets.only(
                top: isFirst ? 6 : 0,
                bottom: isLast ? 6 : 0,
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  SizedBox(
                    height: 40,
                    child: Align(
                      alignment: Alignment.centerRight,
                      child: Row(
                        children: [
                          Expanded(
                            child: Text(
                              stage.label,
                              style: TextStyle(
                                fontSize: 14,
                                fontWeight:
                                    stage.state == _NodeState.inactive
                                        ? FontWeight.normal
                                        : FontWeight.w600,
                                color: stage.state == _NodeState.inactive
                                    ? (isDark
                                        ? Colors.grey[500]
                                        : AppColors.neutral400)
                                    : (isDark
                                        ? Colors.white
                                        : AppColors.neutral800),
                              ),
                              textAlign: TextAlign.right,
                            ),
                          ),
                          if (stage.date != null)
                            Padding(
                              padding: const EdgeInsets.only(right: 8),
                              child: Text(
                                stage.date!,
                                style: TextStyle(
                                  fontSize: 11,
                                  color: isDark
                                      ? Colors.grey[500]
                                      : AppColors.neutral400,
                                ),
                              ),
                            ),
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class _PulseWrapper extends StatefulWidget {
  final Widget child;

  const _PulseWrapper({required this.child});

  @override
  State<_PulseWrapper> createState() => _PulseWrapperState();
}

class _PulseWrapperState extends State<_PulseWrapper>
    with SingleTickerProviderStateMixin {
  late AnimationController _controller;
  late Animation<double> _animation;

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1200),
    )..repeat(reverse: true);
    _animation = Tween<double>(begin: 0.85, end: 1.0).animate(
      CurvedAnimation(parent: _controller, curve: Curves.easeInOut),
    );
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return AnimatedBuilder(
      animation: _animation,
      builder: (context, child) {
        return Transform.scale(
          scale: _animation.value,
          child: child,
        );
      },
      child: widget.child,
    );
  }
}
