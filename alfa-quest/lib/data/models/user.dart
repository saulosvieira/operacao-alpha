import 'package:freezed_annotation/freezed_annotation.dart';

part 'user.freezed.dart';
part 'user.g.dart';

@JsonEnum(alwaysCreate: true)
enum SubscriptionStatus {
  active,
  inactive,
  cancelled,
  pending,
  trial,
}

@freezed
abstract class User with _$User {
  const factory User({
    @JsonKey(fromJson: _idFromJson) required int id,
    required String name,
    required String email,
    String? phone,
    String? role,
    @JsonKey(name: 'subscriptionStatus', unknownEnumValue: SubscriptionStatus.inactive)
    @Default(SubscriptionStatus.inactive) SubscriptionStatus subscriptionStatus,
    @JsonKey(name: 'subscriptionExpiresAt') DateTime? subscriptionExpiresAt,
    @JsonKey(name: 'subscriptionPlatformId') String? subscriptionPlatformId,
  }) = _User;

  factory User.fromJson(Map<String, dynamic> json) => _$UserFromJson(json);
}

/// A API pode retornar id como String ou int
int _idFromJson(dynamic value) {
  if (value is int) return value;
  if (value is String) return int.parse(value);
  return 0;
}

extension UserX on User {
  bool get hasActiveSubscription =>
      subscriptionStatus == SubscriptionStatus.active ||
      subscriptionStatus == SubscriptionStatus.trial;
}
